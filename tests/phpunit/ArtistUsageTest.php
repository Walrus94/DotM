<?php

namespace GazelleUnitTest;

use PHPUnit\Framework\TestCase;

class ArtistUsageTest extends TestCase {
    public function testUsageTotalCountsReleases(): void {
        $db = \Gazelle\DB::DB();
        $artist = (new \Gazelle\Manager\Artist())->create('phpunit artist');
        $aliasId = $artist->primaryAliasId();

        $db->prepared_query(
            "INSERT INTO `release` (Name, Year, catalog_number, record_label, release_type, TagList, WikiBody, WikiImage, created, updated, showcase)
             VALUES ('phpunit release 1', 2000, '', '', 0, '', '', '', now(), now(), 0)"
        );
        $r1 = $db->inserted_id();
        $db->prepared_query(
            "INSERT INTO release_artist (GroupID, AliasID, UserID, Importance, artist_role_id, created)
             VALUES (?, ?, 0, 1, 1, now())",
            $r1, $aliasId
        );

        $db->prepared_query(
            "INSERT INTO `release` (Name, Year, catalog_number, record_label, release_type, TagList, WikiBody, WikiImage, created, updated, showcase)
             VALUES ('phpunit release 2', 2001, '', '', 0, '', '', '', now(), now(), 0)"
        );
        $r2 = $db->inserted_id();
        $db->prepared_query(
            "INSERT INTO release_artist (GroupID, AliasID, UserID, Importance, artist_role_id, created)
             VALUES (?, ?, 0, 1, 1, now())",
            $r2, $aliasId
        );

        $this->assertEqualsCanonicalizing([$r1, $r2], $artist->releaseIdUsage());
        $this->assertSame(2, $artist->usageTotal());

        $db->prepared_query("DELETE FROM release_artist WHERE GroupID IN (?, ?)", $r1, $r2);
        $db->prepared_query("DELETE FROM `release` WHERE ID IN (?, ?)", $r1, $r2);
        $db->prepared_query("DELETE FROM artists_alias WHERE ArtistID = ?", $artist->id());
        $db->prepared_query("DELETE FROM artists_group WHERE ArtistID = ?", $artist->id());
    }
}
