<?php

namespace Gazelle\UserRank\Dimension;

class CollageContribution extends \Gazelle\UserRank\AbstractUserRank {
    public function cacheKey(): string {
        return 'rank_data_collagecontrib';
    }

    public function selector(): string {
        // Note: Modified for music catalog - only count artist contributions, not torrent collages
        return "
            SELECT count(*) AS n
            FROM collages_artists ca
            INNER JOIN collages c ON (c.ID = ca.CollageID)
            INNER JOIN users_main um ON (um.ID = ca.UserID)
            WHERE um.Enabled = '1'
                AND c.Deleted = '0'
                AND c.Locked = '0'
            GROUP BY um.ID
            ORDER BY 1
        ";
    }
}
