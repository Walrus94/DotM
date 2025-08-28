<?php

namespace Gazelle\UserRank\Dimension;

class DataDownload extends \Gazelle\UserRank\AbstractUserRank {
    public function cacheKey(): string {
        return 'rank_data_download';
    }

    public function selector(): string {
        // Note: users_leech_stats table has been removed - download stats are no longer tracked
        // This method is deprecated and will always return empty result
        return "
            SELECT 0 AS n
            FROM users_main um
            WHERE um.Enabled = '1'
            LIMIT 1
        ";
    }
}
