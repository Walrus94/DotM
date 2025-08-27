<?php
use Phinx\Seed\AbstractSeed;

class ArtistReleaseSeeder extends AbstractSeed {
    public function run(): void {
        $db = $this->getAdapter()->getConnection();
        // Temporarily disable FK checks to mirror application create flow
        $db->exec("SET foreign_key_checks = 0");
        // artists_group no longer stores Name; create a group, then create an alias, then set PrimaryAlias
        $db->exec("INSERT INTO artists_group (PrimaryAlias) VALUES (0)");
        $artistId = (int)$db->lastInsertId();
        $db->exec("INSERT INTO artists_alias (ArtistID, Name, UserID, Redirect) VALUES ($artistId, 'demo artist', 0, 0)");
        $aliasId = $db->lastInsertId();
        $db->exec("UPDATE artists_group SET PrimaryAlias = $aliasId WHERE ArtistID = $artistId");
        $db->exec("SET foreign_key_checks = 1");

        $db->exec("INSERT INTO `release` (Name, Year, catalog_number, record_label, release_type, TagList, WikiBody, WikiImage, created, updated, showcase) VALUES ('demo release 1', 2000, '', '', 0, '', '', '', now(), now(), 0)");
        $r1 = $db->lastInsertId();
        $db->exec("INSERT INTO release_artist (GroupID, AliasID, UserID, Importance, artist_role_id, created) VALUES ($r1, $aliasId, NULL, 1, 1, now())");

        $db->exec("INSERT INTO `release` (Name, Year, catalog_number, record_label, release_type, TagList, WikiBody, WikiImage, created, updated, showcase) VALUES ('demo release 2', 2001, '', '', 0, '', '', '', now(), now(), 0)");
        $r2 = $db->lastInsertId();
        $db->exec("INSERT INTO release_artist (GroupID, AliasID, UserID, Importance, artist_role_id, created) VALUES ($r2, $aliasId, NULL, 1, 1, now())");
    }
}
