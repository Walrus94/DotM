<?php

declare(strict_types=1);

namespace Gazelle;

use Gazelle\Enum\TorrentFlag;

class Edition extends TorrentAbstract {
    final public const tableName = 'edition';
    final public const CACHE_KEY = 't_%d';

    public function infoRow(): ?array {
        $info = self::$db->rowAssoc(" 
            SELECT t.GroupID,
                t.UserID,
                t.release_id,
                t.Format,
                t.Encoding,
                t.edition_type,
                t.RemasterYear,
                t.RemasterTitle,
                t.RemasterRecordLabel,
                t.RemasterCatalogueNumber,
                t.Scene,
                t.HasLog,
                t.HasCue,
                t.HasLogDB,
                t.LogScore,
                t.info_hash,
                t.FileCount,
                t.FileList,
                t.FilePath,
                t.Size,
                t.FreeTorrent,
                t.FreeLeechType,
                t.created,
                t.Description,
                t.LastReseedRequest,
                0             AS Seeders,
                0             AS Leechers,
                0             AS Snatched,
                NULL          AS last_action,
                ''            AS ripLogIds
            FROM edition t
            WHERE t.edition_id = ?
            GROUP BY t.edition_id
            ", $this->id
        );
        if ($info) {
            self::$db->prepared_query(" 
                SELECT a.Name
                FROM torrent_attr a JOIN torrent_has_attr ha ON (a.ID = ha.TorrentAttrID)
                WHERE ha.TorrentID = ?
            ", $this->id);
            $info['attr'] = [];
            foreach (self::$db->to_array(escape: false) as $row) {
                $info['attr'][$row['Name']] = true;
            }
        }
        return $info;
    }
}
