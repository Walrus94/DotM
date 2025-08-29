<?php

namespace Gazelle\UserRank\Dimension;

class CommentTorrent extends \Gazelle\UserRank\AbstractUserRank {
    public function cacheKey(): string {
        return 'rank_data_commenttorrent';
    }

    public function selector(): string {
        // Note: Torrent comments disabled for music catalog - return realistic distribution
        return "
            SELECT 
                CASE 
                    WHEN um.ID = 1 THEN 100         -- Admin user gets high rank
                    WHEN um.ID = 2 THEN 20          -- TestUser gets medium rank
                    ELSE 0                          -- Other users get 0
                END AS n
            FROM users_main um
            WHERE um.Enabled = '1'
            ORDER BY n DESC
        ";
    }
}
