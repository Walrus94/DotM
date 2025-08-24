<?php

namespace Gazelle\Manager;

use Gazelle\Enum\LeechType;
use Gazelle\Enum\LeechReason;

class Edition extends \Gazelle\BaseManager {
    protected const ID_KEY = 'zz_e_%d';
    protected const CACHE_HIST = 'top10_hist_%s_%s';

    final public const CACHE_KEY_LATEST_UPLOADS = 'latest_up_%d';
    final protected const CACHE_FOLDERNAME      = 'foldername_%s';

    protected \Gazelle\User $viewer;

    /**
     * Set the viewer context, for snatched indicators etc.
     * If this is set, and Torrent object created will have it set
     */
    public function setViewer(\Gazelle\User $viewer): static {
        $this->viewer = $viewer;
        return $this;
    }

    public function create(
        \Gazelle\TGroup $tgroup,
        \Gazelle\User   $user,
        int             $releaseId,
        string          $description,
        ?string         $format,
        ?string         $encoding,
        string          $infohash,
        string          $filePath,
        array           $fileList,
        int             $size,
        bool            $isScene,
        string          $editionType,
        ?int            $remasterYear,
        string          $remasterTitle,
        string          $remasterRecordLabel,
        string          $remasterCatalogueNumber,
        int             $logScore     = 0,
        bool            $hasCue      = false,
        bool            $hasLog      = false,
        bool            $hasLogInDB  = false,
    ): \Gazelle\Edition {
        self::$db->prepared_query("
            INSERT INTO edition (
                GroupID, UserID, release_id, Format, Encoding, edition_type, RemasterYear, RemasterTitle, RemasterRecordLabel, RemasterCatalogueNumber,
                info_hash, Scene, LogScore, HasLog, HasCue, HasLogDB, FilePath, FileCount, FileList,
                Size, Description
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
                ?, ?
            )", $tgroup->id(), $user->id(), $releaseId, $format, $encoding, $editionType,
                $remasterYear, $remasterTitle, $remasterRecordLabel, $remasterCatalogueNumber,
                $infohash, $isScene ? '1' : '0', $logScore, $hasLog ? '1' : '0',
                $hasCue ? '1' : '0', $hasLogInDB ? '1' : '0', $filePath, count($fileList), implode("\n", $fileList),
                $size, $description,
        );
        $edition = $this->findById(self::$db->inserted_id());
        $tgroup->flush();
        $edition->lockUpload();
        $edition->flushFoldernameCache();
        $user->flushRecentUpload();

        // Flush the most recent uploads when a new lossless upload is made
        if (in_array($encoding, ['Lossless', '24bit Lossless'])) {
            self::$cache->delete_value(sprintf(self::CACHE_KEY_LATEST_UPLOADS, 5));
        }
        return $edition;
    }

    public function findById(int $editionId): ?\Gazelle\Edition {
        $key = sprintf(self::ID_KEY, $editionId);
        $id = self::$cache->get_value($key);
        if ($id === false) {
            $id = self::$db->scalar("
                SELECT edition_id FROM edition WHERE edition_id = ?
                ", $editionId
            );
            if (!is_null($id)) {
                self::$cache->cache_value($key, $id, 7200);
            }
        }
        if (!$id) {
            return null;
        }
        $edition = new \Gazelle\Edition($id);
        if (isset($this->viewer)) {
            $edition->setViewer($this->viewer);
        }
        return $edition;
    }

    public function findDeletedById(int $editionId): ?\Gazelle\TorrentDeleted {
        $found = (bool)self::$db->scalar("
            SELECT 1 FROM deleted_torrents WHERE ID = ?
            ", $editionId
        );
        return $found ? new \Gazelle\TorrentDeleted($editionId) : null;
    }

    public function findByInfohash(string $hash): ?\Gazelle\Edition {
        return $this->findById((int)self::$db->scalar("
            SELECT edition_id FROM edition WHERE info_hash = unhex(?)
            ", $hash
        ));
    }

    /**
     * How many other uploads share the same folder path?
     * NB: Ignore single files that are not in a directory
     *
     * @return array of Gazelle\Torrent objects;
     */
    public function findAllByFoldername(string $folder): array {
        if ($folder === '') {
            return [];
        }
        $key = sprintf(self::CACHE_FOLDERNAME, signature($folder, FOLDER_CLASH_SALT));
        $list = self::$cache->get_value($key);
        if ($list === false) {
            self::$db->prepared_query("
                SELECT t.edition_id
                FROM edition t
                INNER JOIN torrents_group tg ON (tg.ID = t.GroupID)
                WHERE t.FilePath = ?
                ", $folder
            );
            $list = self::$db->collect(0);
            self::$cache->cache_value($key, $list, 7200);
        }
        $all = [];
        foreach ($list as $id) {
            $edition = $this->findById($id);
            if ($edition) {
                $all[] = $edition;
            }
        }
        return $all;
    }

    public function lookupLeechReason(string $leechReason): LeechReason {
        return match ($leechReason) {
            LeechReason::AlbumOfTheMonth->value => LeechReason::AlbumOfTheMonth,
            LeechReason::Permanent->value       => LeechReason::Permanent,
            LeechReason::Showcase->value        => LeechReason::Showcase,
            LeechReason::StaffPick->value       => LeechReason::StaffPick,
            default                             => LeechReason::Normal,
        };
    }

    public function lookupLeechType(string $leechType): LeechType {
        return match ($leechType) {
            LeechType::Free->value    => LeechType::Free,
            LeechType::Neutral->value => LeechType::Neutral,
            default                   => LeechType::Normal,
        };
    }

    public function leechReasonList(): array {
        return [
            LeechReason::Normal,
            LeechReason::AlbumOfTheMonth,
            LeechReason::Permanent,
            LeechReason::Showcase,
            LeechReason::StaffPick,
        ];
    }

    public function leechTypeList(): array {
        return [
            LeechType::Normal,
            LeechType::Neutral,
            LeechType::Free,
        ];
    }

    public function flushFoldernameCache(string $folder): void {
        self::$cache->delete_value(sprintf(self::CACHE_FOLDERNAME, signature($folder, FOLDER_CLASH_SALT)));
    }

    public function missingLogfiles(int $userId): array {
        self::$db->prepared_query("
            SELECT ID FROM torrents WHERE HasLog = '1' AND HasLogDB = '0' AND UserID = ?
            ", $userId
        );
        $torrentIds = self::$db->collect(0, false);

        $result = [];
        foreach ($torrentIds as $torrentId) {
            $result[$torrentId] = $this->findById($torrentId);
        }
        return $result;
    }

    /**
     * Return a list of all CD+Log uploads of a user. As this list can be large.
     * only id + name fields are returned to avoid excessive memory consumption.
     * Should probably become paginated.
     *
     * @return array of ['id', 'link']
     */
    public function logFileList(int $userId): array {
        self::$db->prepared_query("
            SELECT ID FROM torrents WHERE HasLog = '1' AND HasLogDB = '1' AND UserID = ?
            ", $userId
        );
        $torrentIds = self::$db->collect(0, false);

        $result = [];
        foreach ($torrentIds as $torrentId) {
            $torrent = $this->findById($torrentId);
            if ($torrent) {
                $result[$torrentId] = [
                    'id'   => $torrent->id(),
                    'link' => $torrent->fullLink(),
                ];
            }
        }
        return $result;
    }

    /**
     * Create a string that contains file info in a format that's easy to use for Sphinx
     * NB: This is defined here because upload_handle needs it before a torrent is created
     *
     * @return string with the format .EXT sSIZEs NAME DELIMITER
     */
    public function metaFilename(string $name, int $size): string {
        $name = make_utf8(strtr($name, "\n\r\t", '   '));
        $extPos = mb_strrpos($name, '.');
        $ext = $extPos === false ? '' : trim(mb_substr($name, $extPos + 1));
        return sprintf(".%s s%ds %s %s", $ext, $size, $name, FILELIST_DELIM);
    }

    public function setSourceFlag(\OrpheusNET\BencodeTorrent\BencodeTorrent $torrent): bool {
        $torrentSource = $torrent->getSource();
        if ($torrentSource === SOURCE) {
            return false;
        }
        $creationDate = $torrent->getCreationDate();
        if (!is_null($creationDate)) {
            if (is_null($torrentSource) && $creationDate <= GRANDFATHER_OLD_SOURCE) {
                return false;
            } elseif (!is_null($torrentSource) && $torrentSource === GRANDFATHER_SOURCE && $creationDate <= GRANDFATHER_OLD_SOURCE) {
                return false;
            }
        }
        return $torrent->setSource(SOURCE);
    }

    /**
     * Record who's seeding how much, used for ratio watch
     */
    public function updateSeedingHistory(): array {
        self::$db->dropTemporaryTable("tmp_users_torrent_history");
        self::$db->prepared_query("
            CREATE TEMPORARY TABLE tmp_users_torrent_history (
                UserID int NOT NULL PRIMARY KEY,
                NumTorrents int NOT NULL DEFAULT 0,
                SumTime bigint NOT NULL DEFAULT 0,
                SeedingAvg int NOT NULL DEFAULT 0,
                KEY numtorrents_idx (NumTorrents)
            ) ENGINE=InnoDB
        ");

        // Find seeders that have announced within the last hour
        self::$db->prepared_query("
            INSERT INTO tmp_users_torrent_history
                (UserID, NumTorrents)
            SELECT uid, COUNT(DISTINCT fid)
            FROM xbt_files_users
            WHERE mtime > unix_timestamp(now() - INTERVAL 1 HOUR)
                AND Remaining = 0
            GROUP BY uid
        ");
        $info = ['new' => self::$db->affected_rows()];

        // Mark new records as "checked" and set the current time as the time
        // the user started seeding <NumTorrents> seeded.
        // Finished = 1 means that the user hasn't been seeding exactly <NumTorrents> earlier today.
        // This query will only do something if the next one inserted new rows last hour.
        self::$db->prepared_query("
            UPDATE users_torrent_history AS h
            INNER JOIN tmp_users_torrent_history AS t ON (t.UserID = h.UserID AND t.NumTorrents = h.NumTorrents)
            SET h.Finished = '0',
                h.LastTime = UNIX_TIMESTAMP(now())
            WHERE h.Finished = '1'
                AND h.Date = UTC_DATE() + 0
        ");
        $info['updated'] = self::$db->affected_rows();

        // Insert new rows for users who haven't been seeding exactly <NumTorrents> torrents earlier today
        // and update the time spent seeding <NumTorrents> torrents for the others.
        // Primary table index: (UserID, NumTorrents, Date).
        self::$db->prepared_query("
            INSERT INTO users_torrent_history
                (UserID, NumTorrents, Date)
            SELECT UserID, NumTorrents, UTC_DATE() + 0
            FROM tmp_users_torrent_history
            ON DUPLICATE KEY UPDATE
                Time = Time + UNIX_TIMESTAMP(now()) - LastTime,
                LastTime = UNIX_TIMESTAMP(now())
        ");
        $info['history'] = self::$db->affected_rows();
        self::$db->dropTemporaryTable("tmp_users_torrent_history");
        return $info;
    }

    /**
     * Freeleech / neutral leech / normalise a list of torrents
     * NB: If FLAC-only filtering is required, it must be handled by the calling code.
     */
    public function setListFreeleech(
        \Gazelle\Tracker $tracker,
        \Gazelle\User    $user,
        array            $idList,
        LeechType        $leechType,
        LeechReason      $reason
    ): int {
        $placeholders = placeholders($idList);
        self::$db->prepared_query("
            UPDATE torrents SET
                FreeTorrent = ?, FreeLeechType = ?
            WHERE ID IN ($placeholders)
            ", $leechType->value, $reason->value, ...$idList
        );
        $affected = self::$db->affected_rows();

        $refresh = [];
        foreach ($idList as $torrentId) {
            $torrent = $this->findById($torrentId)->flush();
            $tracker->modifyTorrent($torrent, $leechType);
            $tgroupId = $torrent->groupId();
            if (!isset($refresh[$tgroupId])) {
                $refresh[$tgroupId] = $torrent->group();
            }
            $this->logger()
                ->torrent(
                    $torrent,
                    $user,
                    "marked as freeleech type {$reason->label()}"
                )
                ->general(
                    "{$user->username()} marked torrent $torrentId freeleech type {$reason->label()}"
                );
        }
        foreach ($refresh as $tgroup) {
            $tgroup->refresh();
        }

        return $affected;
    }
    /**
     * Return the N most recent lossless uploads
     * Note that if both a Lossless and 24bit Lossless are uploaded at the same time,
     * only one entry will be returned, to ensure that the result is comprised of N
     * different groups. Uploads of paranoid and disabled users are excluded.
     * Uploads without cover art are excluded.
     *
     * @return array of \Gazelle\Torrent objects
     */
    public function latestUploads(int $limit): array {
        $key    = sprintf(self::CACHE_KEY_LATEST_UPLOADS, $limit);
        $latest = self::$cache->get_value($key);
        $retry  = 2;
        while ($retry--) {
            if ($latest === false) {
                self::$db->prepared_query("
                    SELECT t.GroupID                    AS group_id,
                        t.ID                            AS torrent_id,
                        coalesce(um.Paranoia, 'a:0:{}') AS paranoia
                    FROM torrents t
                    INNER JOIN torrents_group tg ON (tg.ID = t.GroupID)
                    INNER JOIN users_main     um ON (um.ID = t.UserID)
                    WHERE t.created > now() - INTERVAL 3 DAY
                        AND t.Encoding IN ('Lossless', '24bit Lossless')
                        AND tg.WikiImage REGEXP '^https?://[^/]+/.*'
                        AND um.Enabled = '1'
                        AND NOT EXISTS (
                            SELECT 1
                            FROM release_tag ttex
                            WHERE t.GroupID = ttex.release_id
                                AND ttex.TagID IN (" . placeholders(HOMEPAGE_TAG_IGNORE) . ")
                        )
                    ORDER BY t.created DESC
                    ", ...HOMEPAGE_TAG_IGNORE
                );
                $latest = [];
                $seen   = [];
                $nr     = 0;
                $qid    = self::$db->get_query_id();
                $max    = self::$db->record_count();
                while ($nr < min($limit, $max)) {
                    self::$db->set_query_id($qid);
                    $row = self::$db->next_record(MYSQLI_ASSOC, false);
                    if (is_null($row)) {
                        break;
                    }
                    if (in_array($row['group_id'], $seen)) {
                        continue;
                    }
                    if (in_array('uploads', unserialize($row['paranoia']))) {
                        continue;
                    }
                    $torrent = $this->findById($row['torrent_id']);
                    if (is_null($torrent) || !$torrent->group()->image()) {
                        continue;
                    }
                    $seen[]   = $row['group_id'];
                    $latest[] = $row['torrent_id'];
                    ++$nr;
                }
                self::$cache->cache_value($key, $latest, 3600);
            }
            // we either have a fresh list of ids, or it came from the cache
            // reinstantiate the list as Torrent objects
            $list = array_filter(
                array_map(fn($id) => $this->findById($id), $latest),
                fn($t) => $t instanceof \Gazelle\Torrent && !is_null($t->group()->image())
            );
            if (count($list) == count($latest)) {
                return $list;
            }
            // a torrent was deleted since the list was cached: must regenerate a new list
            $latest = false;
        }
        return [];
    }

    /**
     * This is a bit of an ugly hack, used to render PL bbcode links from the \Text class
     */

    public static function renderPL(int $id, array $attr): ?string {
        $torrent = (new self())->findById($id);
        $meta = '';
        $wantMeta = !(in_array('nometa', $attr) || in_array('title', $attr));

        if (!is_null($torrent)) {
            $tgroup = $torrent->group();
            if ($wantMeta && $tgroup->categoryName() === 'Music') {
                $meta = self::metaPL(
                    $torrent->media(), $torrent->format(), $torrent->encoding(),
                    $torrent->hasCue(), $torrent->hasLog(), $torrent->hasLogDb(), $torrent->logScore()
                );
            }
            $isDeleted = false;
        } else {
            $deleted = self::$db->rowAssoc("
                SELECT GroupID, Format, Encoding, Media, HasCue, HasLog, HasLogDB, LogScore
                FROM deleted_torrents
                WHERE ID = ?
                ", $id
            );
            if (is_null($deleted)) {
                return null;
            }
            $tgroup = (new \Gazelle\Manager\TGroup())->findById((int)$deleted['GroupID']);
            if (is_null($tgroup)) {
                return null;
            }
            if ($wantMeta && $tgroup->categoryName() === 'Music') {
                $meta = self::metaPL(
                    $deleted['Media'], $deleted['Format'], $deleted['Encoding'],
                    (bool)$deleted['HasCue'], (bool)$deleted['HasLog'], (bool)$deleted['HasLogDB'], (int)$deleted['LogScore']
                );
            }
            $isDeleted = true;
        }
        $year = in_array('noyear', $attr) || in_array('title', $attr) ? '' : $tgroup->year();
        $releaseType = ($tgroup->categoryName() !== 'Music' || in_array('noreleasetype', $attr) || in_array('title', $attr))
            ? '' : $tgroup->releaseTypeName();
        $label = ($year || $releaseType) ? (' [' . trim(implode(' ', [$year, $releaseType])) . ']') : '';
        $url = '';
        if (!(in_array('noartist', $attr) || in_array('title', $attr))) {
            if ($tgroup->categoryName() === 'Music') {
                $url = $tgroup->artistRole()->link() . " – ";
            }
        }
        return $url . sprintf(
            '<a title="%s" href="/torrents.php?id=%d&torrentid=%d#torrent%d">%s%s</a>%s',
            $tgroup->hashTag(), $tgroup->id(), $id, $id, display_str($tgroup->name()), $label,
            $meta . ($isDeleted ? ' <i>deleted</i>' : '')
        );
    }

    protected static function metaPL(
        string $media,
        string $format,
        string $encoding,
        bool $hasCue,
        bool $hasLog,
        bool $hasLogDb,
        int $logScore,
    ): string {
        $meta = [$media, $format, $encoding];
        if ($hasCue) {
            $meta[] = 'Cue';
        }
        if ($hasLog) {
            $log = 'Log';
            if ($hasLogDb) {
                $log .= " ({$logScore}%)";
            }
            $meta[] = "$log";
        }
        return ' ' . implode('/', $meta);
    }

    public function topTenCacheKey(string $datetime, bool $isByDay): string {
        return sprintf(self::CACHE_HIST,
            $isByDay ? 'day' : 'week',
            preg_replace('/\D+/', '', $datetime)
        );
    }
    public function topTenHistoryList(string $datetime, bool $isByDay): array {
        $key = $this->topTenCacheKey($datetime, $isByDay);
        $list = self::$cache->get_value($key);
        if ($list === false) {
            self::$db->prepared_query("
                SELECT tht.sequence,
                    tht.TorrentID AS torrent_id
                FROM top10_history th
                INNER JOIN top10_history_torrents tht ON (tht.HistoryID = th.ID)
                INNER JOIN torrents t ON (t.ID = tht.TorrentID)
                WHERE th.Type    = ?
                    AND th.Date >= date(?)
                    AND th.Date <  date(?) + INTERVAL ? DAY
                ORDER BY tht.sequence ASC
                ", $isByDay ? 'Daily' : 'Weekly', $datetime, $datetime, $isByDay ? 1 : 7
            );
            $list = self::$db->to_array(false, MYSQLI_ASSOC, false);
            self::$cache->cache_value($key, $list, 3600 * 24);
        }
        foreach ($list as &$entry) {
            $entry['torrent'] = $this->findById($entry['torrent_id']);
        }
        return $list;
    }
}
