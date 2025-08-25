<?php

namespace Gazelle\UserRank\Dimension;

class ArtistsAdded extends \Gazelle\UserRank\AbstractUserRank {
    public function cacheKey(): string {
        return 'rank_data_artistsadded';
    }

    public function selector(): string {
        return "
            SELECT count(*) AS n
            FROM release_artist AS ta
            INNER JOIN torrents_group AS tg ON (tg.ID = ta.release_id)
            INNER JOIN torrents AS t ON (t.GroupID = tg.ID)
            INNER JOIN users_main AS um ON (um.ID = ta.UserID)
            WHERE t.UserID != ta.UserID
                AND um.Enabled = '1'
            GROUP BY tg.ID
            ORDER BY 1
        ";
    }
}
