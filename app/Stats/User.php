<?php

namespace Gazelle\Stats;

class User extends \Gazelle\BaseObject {
    /**
     * This class offloads all the counting operations you might
     * want to do with a User (so that the User class does not
     * grow to an unmanageable size).
     *
     * Counting things relating to collections of users are found
     * in the Users (plural) class.
     */

    final public const tableName              = 'user_summary';
    final protected const CACHE_COMMENT_TOTAL = 'user_nrcomment_%d';
    final protected const CACHE_GENERAL       = 'user_stx_%d';

    // Cache the underlying db calls
    protected array $commentTotal;

    public function flush(): static {
        $this->info = [];
        self::$cache->delete_multi([
            sprintf(self::CACHE_COMMENT_TOTAL, $this->id),
            sprintf(self::CACHE_GENERAL, $this->id),
        ]);
        return $this;
    }

    public function link(): string {
        return sprintf('<a href="%s">%s</a>', $this->url(), 'Stats');
    }

    public function location(): string {
        return 'user.php?action=stats&userid=' . $this->id;
    }

    /**
     * Get the total number of comments made by page type
     *
     * @param string $page name [artist, collages requests torrents]
     * @return int number of comments, 0 if page is invalid
     */
    public function commentTotal(string $page): int {
        if (!isset($this->commentTotal)) {
            $key = sprintf(self::CACHE_COMMENT_TOTAL, $this->id);
            $commentTotal = self::$cache->get_value($key);
            if ($commentTotal === false) {
                self::$db->prepared_query("
                    SELECT Page, count(*) as n
                    FROM comments
                    WHERE AuthorID = ?
                    GROUP BY Page
                    ", $this->id
                );
                $commentTotal = self::$db->to_pair('Page', 'n', false);
                self::$cache->cache_value($key, $commentTotal, 3600);
            }
            $this->commentTotal = $commentTotal;
        }
        return $this->commentTotal[$page] ?? 0;
    }

    /**
     * @see \Gazelle\Stats\Users::refresh()
     */
    public function info(): array {
        if (isset($this->info) && !empty($this->info)) {
            return $this->info;
        }
        $key = sprintf(self::CACHE_GENERAL, $this->id);
        $info = self::$cache->get_value($key);
        if ($info === false) {
            // If a user has done nothing so far (no collages, no downloads...)
            // they will have no row in user_summary as yet, hence the need
            // to fallback on an array with values of 0
            $info = self::$db->rowAssoc("
                SELECT artist_added_total,
                    collage_total,
                    collage_contrib,
                    download_total,
                    download_unique,
                    fl_token_total,
                    forum_post_total,
                    forum_thread_total,
                    invited_total,
                    leech_total,
                    perfect_flac_total,
                    perfecter_flac_total,
                    request_bounty_total,
                    request_bounty_size,
                    request_created_total,
                    request_created_size,
                    request_vote_total,
                    request_vote_size,
                    seeding_total,
                    seedtime_hour,
                    snatch_total,
                    snatch_unique,
                    unique_group_total,
                    upload_total
                FROM user_summary
                WHERE user_id = ?
                ", $this->id
            ) ?? [
                'artist_added_total'    => 0,
                'collage_total'         => 0,
                'collage_contrib'       => 0,
                'download_total'        => 0,
                'download_unique'       => 0,
                'fl_token_total'        => 0,
                'forum_post_total'      => 0,
                'forum_thread_total'    => 0,
                'invited_total'         => 0,
                'leech_total'           => 0,
                'perfect_flac_total'    => 0,
                'perfecter_flac_total'  => 0,
                'request_bounty_total'  => 0,
                'request_bounty_size'   => 0,
                'request_created_total' => 0,
                'request_created_size'  => 0,
                'request_vote_total'    => 0,
                'request_vote_size'     => 0,
                'seeding_total'         => 0,
                'seedtime_hour'         => 0,
                'snatch_total'          => 0,
                'snatch_unique'         => 0,
                'unique_group_total'    => 0,
                'upload_total'          => 0,
            ];
            self::$cache->cache_value($key, $info, 300);
        }
        $this->info = $info;
        return $this->info;
    }

    /**
     * Some statistics can be updated immediately, such as download_total.
     * Others, like download_unique need a possibly expensive check.
     * In any case, those stats will be updated within the hour.
     * If we can update immediately, though, we can do it here.
     */
    public function increment(string $name, int $incr = 1): int {
        self::$db->prepared_query("
            UPDATE user_summary SET
                $name = $name + ?
            WHERE user_id = ?
            ", $incr, $this->id
        );
        $this->info = [];
        self::$cache->delete_value(sprintf(self::CACHE_GENERAL, $this->id));
        return self::$db->affected_rows();
    }

    public function artistAddedTotal(): int {
        return $this->info()['artist_added_total'];
    }

    public function collageTotal(): int {
        return $this->info()['collage_total'];
    }

    public function collageContrib(): int {
        return $this->info()['collage_contrib'];
    }

    public function forumPostTotal(): int {
        return $this->info()['forum_post_total'];
    }

    public function forumThreadTotal(): int {
        return $this->info()['forum_thread_total'];
    }

    public function invitedTotal(): int {
        return $this->info()['invited_total'];
    }

    public function timeline(): array {
        $key = "u_statgraphs_" . $this->id;
        $charts = self::$cache->get_value($key);
        if ($charts === false) {
            $charts = [
                ['name' => 'daily',   'interval' => 1,      'count' => 24],
                ['name' => 'monthly', 'interval' => 24,     'count' => 30],
                ['name' => 'yearly',  'interval' => 24 * 7, 'count' => 52],
            ];
            foreach ($charts as &$chart) {
                // Note: Torrent-related stats disabled for music catalog
                // Return empty timeline data for now
                $chart['data_up'] = [];
                $chart['data_down'] = [];
                $chart['buffer'] = [];
                $chart['uploads'] = [];
                $chart['start'] = null;
                unset($chart);
            }
            self::$cache->cache_value($key, $charts, 3600);
        }
        return $charts;
    }
}
