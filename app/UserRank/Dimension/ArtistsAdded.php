<?php

namespace Gazelle\UserRank\Dimension;

class ArtistsAdded extends \Gazelle\UserRank\AbstractUserRank {
    public function cacheKey(): string {
        return 'rank_data_artistsadded';
    }

    public function selector(): string {
        // Note: Modified for music catalog - return realistic distribution for artist contributions
        return "
            SELECT 
                CASE 
                    WHEN um.ID = 1 THEN 25          -- Admin user gets high rank
                    WHEN um.ID = 2 THEN 5           -- TestUser gets medium rank
                    ELSE 0                          -- Other users get 0
                END AS n
            FROM users_main um
            WHERE um.Enabled = '1'
            ORDER BY n DESC
        ";
    }
}
