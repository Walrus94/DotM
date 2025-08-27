<?php

namespace GazelleUnitTest;

use PHPUnit\Framework\TestCase;

class ArtistAutocompleteTest extends TestCase {
    public function testAutocompleteUsesReleaseData(): void {
        $db = \Gazelle\DB::DB();
        $man = new \Gazelle\Manager\Artist();
        $artist = $man->create('phpunit autocomplete artist');

        $db->prepared_query(
            "INSERT INTO `release` (Name, Year, catalog_number, record_label, release_type, TagList, WikiBody, WikiImage, created, updated, showcase)
             VALUES ('phpunit auto release', 2020, '', '', 0, '', '', '', now(), now(), 0)"
        );
        $rId = $db->inserted_id();
        $db->prepared_query(
            "INSERT INTO release_artist (GroupID, AliasID, UserID, Importance, artist_role_id, created)
             VALUES (?, ?, 0, 1, 1, now())",
            $rId, $artist->primaryAliasId()
        );

        $list = $man->autocompleteList('phpunit aut');
        $this->assertNotEmpty(array_filter($list, fn($r) => $r['data'] === $artist->id()));

        $db->prepared_query("DELETE FROM release_artist WHERE GroupID = ?", $rId);
        $db->prepared_query("DELETE FROM `release` WHERE ID = ?", $rId);
        $db->prepared_query("DELETE FROM artists_alias WHERE ArtistID = ?", $artist->id());
        $db->prepared_query("DELETE FROM artists_group WHERE ArtistID = ?", $artist->id());
    }
}
