<?php

use Phinx\Seed\AbstractSeed;

class DemoArtist extends AbstractSeed {
    public function run(): void {
        $now = date('Y-m-d H:i:s');

        // temporarily disable foreign key checks to resolve circular references
        $this->execute('SET FOREIGN_KEY_CHECKS = 0');

        // main artist row
        $this->table('artists_group')->insert([
            'ArtistID'      => 1,
            'RevisionID'    => 1,
            'VanityHouse'   => 0,
            'LastCommentID' => 0,
            'PrimaryAlias'  => 1,
        ])->save();

        // artist alias used for lookups
        $this->table('artists_alias')->insert([
            'AliasID'   => 1,
            'ArtistID'  => 1,
            'Name'      => 'Demo Artist',
            'Redirect'  => 0,
            'UserID'    => 1,
        ])->save();

        // simple wiki entry so the artist page loads cleanly
        $this->table('wiki_artists')->insert([
            'RevisionID' => 1,
            'PageID'    => 1,
            'Body'      => 'Seeded demo artist for local testing.',
            'UserID'    => 1,
            'Summary'   => 'initial seed',
            'Time'      => $now,
            'Image'     => null,
        ])->save();

        // create a single release for the artist
        $this->table('release')->insert([
            'ID'            => 1,
            'ArtistID'      => 0,
            'Name'          => 'Demo Release',
            'Year'          => 2024,
            'catalog_number'=> '',
            'record_label'  => '',
            'release_type'  => 1,
            'TagList'       => 'demo',
            'WikiBody'      => 'Demo release inserted via seed.',
            'WikiImage'     => '',
            'showcase'      => 0,
        ])->save();

        // map artist to the release (legacy schema uses GroupID)
        $this->table('release_artist')->insert([
            'GroupID'    => 1,
            'ArtistID'   => 1,
            'AliasID'    => 1,
            'UserID'     => 1,
            'Importance' => 1,
        ])->save();

        // provide a platform link for the release
        $this->table('release_platform')->insert([
            'ID'        => 1,
            'ReleaseID' => 1,
            'Platform'  => 'Bandcamp',
            'Url'       => 'https://bandcamp.example/demo-release',
            'created'   => $now,
            'updated'   => $now,
        ])->save();

        // re-enable foreign key checks
        $this->execute('SET FOREIGN_KEY_CHECKS = 1');
    }
}
