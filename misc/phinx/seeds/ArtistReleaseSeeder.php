<?php
use Phinx\Seed\AbstractSeed;

class ArtistReleaseSeeder extends AbstractSeed {
    public function run(): void {
        $db = $this->getAdapter()->getConnection();
        $db->exec("INSERT INTO artists_group (Name, VanityHouse, WikiBody, WikiImage) VALUES ('demo artist', 0, '', '')");
        $artistId = $db->lastInsertId();
        $db->exec("INSERT INTO artists_alias (ArtistID, Name, UserID, Redirect, RevisionID) VALUES ($artistId, 'demo artist', 0, 0, 0)");
        $aliasId = $db->lastInsertId();

        $db->exec("INSERT INTO `release` (Name, Year, catalog_number, record_label, release_type, TagList, WikiBody, WikiImage, created, updated, showcase) VALUES ('demo release 1', 2000, '', '', 0, '', '', '', now(), now(), 0)");
        $r1 = $db->lastInsertId();
        $db->exec("INSERT INTO release_artist (GroupID, AliasID, UserID, Importance, artist_role_id, created) VALUES ($r1, $aliasId, 0, 1, 1, now())");

        $db->exec("INSERT INTO `release` (Name, Year, catalog_number, record_label, release_type, TagList, WikiBody, WikiImage, created, updated, showcase) VALUES ('demo release 2', 2001, '', '', 0, '', '', '', now(), now(), 0)");
        $r2 = $db->lastInsertId();
        $db->exec("INSERT INTO release_artist (GroupID, AliasID, UserID, Importance, artist_role_id, created) VALUES ($r2, $aliasId, 0, 1, 1, now())");
    }
}
