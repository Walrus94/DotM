<?php

namespace Gazelle\UserRank\Dimension;

class DataUpload extends \Gazelle\UserRank\AbstractUserRank {
    public function cacheKey(): string {
        return 'rank_data_uploadd';
    }

    public function selector(): string {
        // Note: users_leech_stats table has been removed - upload stats are no longer tracked
        // This method is deprecated and will always return empty result
        return "
            SELECT 0 AS n
            FROM users_main um
            WHERE um.Enabled = '1'
            LIMIT 1
        ";
    }
}
