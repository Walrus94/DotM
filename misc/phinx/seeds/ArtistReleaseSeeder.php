<?php
use Phinx\Seed\AbstractSeed;

class ArtistReleaseSeeder extends AbstractSeed {
    public function run(): void {
        $db = $this->getAdapter()->getConnection();
        // Temporarily disable FK checks to mirror application create flow
        $db->exec("SET foreign_key_checks = 0");
        // Reuse existing demo artist if present; otherwise create it
        $aliasId = null;
        $artistId = null;
        $stmt = $db->query("SELECT AliasID, ArtistID FROM artists_alias WHERE Name = 'demo artist' AND Redirect = 0 LIMIT 1");
        $row = $stmt ? $stmt->fetch(\PDO::FETCH_ASSOC) : false;
        if ($row && isset($row['AliasID'])) {
            $aliasId = (int)$row['AliasID'];
            $artistId = (int)$row['ArtistID'];
        } else {
            // artists_group no longer stores Name; create a group, then create an alias, then set PrimaryAlias
            $db->exec("INSERT INTO artists_group (PrimaryAlias) VALUES (0)");
            $artistId = (int)$db->lastInsertId();
            $db->exec("INSERT INTO artists_alias (ArtistID, Name, UserID, Redirect) VALUES ($artistId, 'demo artist', 0, 0)");
            $aliasId = (int)$db->lastInsertId();
            $db->exec("UPDATE artists_group SET PrimaryAlias = $aliasId WHERE ArtistID = $artistId");
        }
        $db->exec("SET foreign_key_checks = 1");

        // Helper to insert a release only if it does not already exist
        $insertRelease = function(string $name, int $year) use ($db, $aliasId): void {
            $check = $db->query("SELECT ID FROM `release` WHERE Name = '" . addslashes($name) . "' LIMIT 1");
            $exists = $check && ($row = $check->fetch(\PDO::FETCH_NUM));
            if ($exists) { return; }
            $db->exec("INSERT INTO `release` (Name, Year, catalog_number, record_label, release_type, TagList, WikiBody, WikiImage, created, updated, showcase) VALUES ('" . addslashes($name) . "', $year, '', '', 0, '', '', '', now(), now(), 0)");
            $r = (int)$db->lastInsertId();
            $db->exec("INSERT INTO release_artist (GroupID, AliasID, UserID, Importance, artist_role_id, created) VALUES ($r, $aliasId, NULL, 1, 1, now())");
        };

        $insertRelease('demo release 1', 2000);
        $insertRelease('demo release 2', 2001);
        // Additional release for manual testing of release view
        $insertRelease('demo release 3', 2002);
    }
}
