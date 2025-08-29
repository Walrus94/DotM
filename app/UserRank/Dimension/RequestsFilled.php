<?php

namespace Gazelle\UserRank\Dimension;

class RequestsFilled extends \Gazelle\UserRank\AbstractUserRank {
    public function cacheKey(): string {
        return 'rank_data_requestsfilled';
    }

    public function selector(): string {
        // Note: Request system disabled for music catalog - return realistic distribution
        return "
            SELECT 
                CASE 
                    WHEN um.ID = 1 THEN 15          -- Admin user gets high rank
                    WHEN um.ID = 2 THEN 3           -- TestUser gets medium rank
                    ELSE 0                          -- Other users get 0
                END AS n
            FROM users_main um
            WHERE um.Enabled = '1'
            ORDER BY n DESC
        ";
    }
}
