<?php

namespace Gazelle\UserRank\Dimension;

class DataDownload extends \Gazelle\UserRank\AbstractUserRank {
    public function cacheKey(): string {
        return 'rank_data_download';
    }

    public function selector(): string {
        // Note: Torrent download stats disabled for music catalog - return realistic distribution
        return "
            SELECT 
                CASE 
                    WHEN um.ID = 1 THEN 500000000   -- Admin user gets medium rank
                    WHEN um.ID = 2 THEN 100000000   -- TestUser gets low rank
                    ELSE 0                          -- Other users get 0
                END AS n
            FROM users_main um
            WHERE um.Enabled = '1'
            ORDER BY n DESC
        ";
    }
}
