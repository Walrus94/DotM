<?php

use Phinx\Migration\AbstractMigration;

class RenameTorrentsToReleasesTables extends AbstractMigration
{
    /**
     * Rename core database tables from torrents_group to releases_group and torrents to releases
     * This is a significant refactoring to improve naming consistency
     */
    public function up()
    {
        // Step 1: Rename torrents_group to releases_group
        $this->execute('RENAME TABLE torrents_group TO releases_group');
        
        // Step 2: Rename torrents to releases
        $this->execute('RENAME TABLE torrents TO releases');
        
        // Step 3: Update foreign key constraints that reference torrents_group
        $this->execute('ALTER TABLE torrents_artists DROP FOREIGN KEY IF EXISTS torrents_artists_ibfk_1');
        $this->execute('ALTER TABLE torrents_artists ADD CONSTRAINT torrents_artists_ibfk_1 FOREIGN KEY (GroupID) REFERENCES releases_group (ID) ON DELETE CASCADE');
        
        $this->execute('ALTER TABLE torrents_tags DROP FOREIGN KEY IF EXISTS torrents_tags_ibfk_1');
        $this->execute('ALTER TABLE torrents_tags ADD CONSTRAINT torrents_tags_ibfk_1 FOREIGN KEY (GroupID) REFERENCES releases_group (ID) ON DELETE CASCADE');
        
        $this->execute('ALTER TABLE collages_torrents DROP FOREIGN KEY IF EXISTS collages_torrents_ibfk_1');
        $this->execute('ALTER TABLE collages_torrents ADD CONSTRAINT collages_torrents_ibfk_1 FOREIGN KEY (GroupID) REFERENCES releases_group (ID) ON DELETE CASCADE');
        
        $this->execute('ALTER TABLE bookmarks_torrents DROP FOREIGN KEY IF EXISTS bookmarks_torrents_ibfk_1');
        $this->execute('ALTER TABLE bookmarks_torrents ADD CONSTRAINT bookmarks_torrents_ibfk_1 FOREIGN KEY (GroupID) REFERENCES releases_group (ID) ON DELETE CASCADE');
        
        $this->execute('ALTER TABLE users_votes DROP FOREIGN KEY IF EXISTS users_votes_ibfk_1');
        $this->execute('ALTER TABLE users_votes ADD CONSTRAINT users_votes_ibfk_1 FOREIGN KEY (GroupID) REFERENCES releases_group (ID) ON DELETE CASCADE');
        
        $this->execute('ALTER TABLE torrents_votes DROP FOREIGN KEY IF EXISTS torrents_votes_ibfk_1');
        $this->execute('ALTER TABLE torrents_votes ADD CONSTRAINT torrents_votes_ibfk_1 FOREIGN KEY (GroupID) REFERENCES releases_group (ID) ON DELETE CASCADE');
        
        $this->execute('ALTER TABLE requests DROP FOREIGN KEY IF EXISTS requests_ibfk_1');
        $this->execute('ALTER TABLE requests ADD CONSTRAINT requests_ibfk_1 FOREIGN KEY (GroupID) REFERENCES releases_group (ID) ON DELETE CASCADE');
        
        // Step 4: Update foreign key constraints that reference torrents
        $this->execute('ALTER TABLE users_torrent_history_snatch DROP FOREIGN KEY IF EXISTS users_torrent_history_snatch_ibfk_1');
        $this->execute('ALTER TABLE users_torrent_history_snatch ADD CONSTRAINT users_torrent_history_snatch_ibfk_1 FOREIGN KEY (TorrentID) REFERENCES releases (ID) ON DELETE CASCADE');
        
        $this->execute('ALTER TABLE torrents_leech_stats DROP FOREIGN KEY IF EXISTS torrents_leech_stats_ibfk_1');
        $this->execute('ALTER TABLE torrents_leech_stats ADD CONSTRAINT torrents_leech_stats_ibfk_1 FOREIGN KEY (TorrentID) REFERENCES releases (ID) ON DELETE CASCADE');
        
        // Step 5: Update foreign key constraint in releases table (formerly torrents)
        $this->execute('ALTER TABLE releases DROP FOREIGN KEY IF EXISTS releases_ibfk_1');
        $this->execute('ALTER TABLE releases ADD CONSTRAINT releases_ibfk_1 FOREIGN KEY (GroupID) REFERENCES releases_group (ID) ON DELETE CASCADE');
        
        // Step 6: Update indexes to reflect new table names
        $this->execute('ALTER TABLE releases_group RENAME INDEX torrents_group_ibfk_1 TO releases_group_ibfk_1');
        $this->execute('ALTER TABLE releases RENAME INDEX torrents_ibfk_1 TO releases_ibfk_1');
        
        // Step 7: Update any remaining foreign key constraints
        $this->execute('ALTER TABLE deleted_torrents_group DROP FOREIGN KEY IF EXISTS deleted_torrents_group_ibfk_1');
        $this->execute('ALTER TABLE deleted_torrents_group ADD CONSTRAINT deleted_torrents_group_ibfk_1 FOREIGN KEY (ArtistID) REFERENCES artists (ID) ON DELETE SET NULL');
    }
    
    public function down()
    {
        // Revert all changes in reverse order
        
        // Step 1: Revert foreign key constraints
        $this->execute('ALTER TABLE deleted_torrents_group DROP FOREIGN KEY IF EXISTS deleted_torrents_group_ibfk_1');
        $this->execute('ALTER TABLE deleted_torrents_group ADD CONSTRAINT deleted_torrents_group_ibfk_1 FOREIGN KEY (ArtistID) REFERENCES artists (ID) ON DELETE SET NULL');
        
        // Step 2: Revert index names
        $this->execute('ALTER TABLE releases_group RENAME INDEX releases_group_ibfk_1 TO torrents_group_ibfk_1');
        $this->execute('ALTER TABLE releases RENAME INDEX releases_ibfk_1 TO torrents_ibfk_1');
        
        // Step 3: Revert foreign key constraints in releases table
        $this->execute('ALTER TABLE releases DROP FOREIGN KEY IF EXISTS releases_ibfk_1');
        $this->execute('ALTER TABLE releases ADD CONSTRAINT torrents_ibfk_1 FOREIGN KEY (GroupID) REFERENCES torrents_group (ID) ON DELETE CASCADE');
        
        // Step 4: Revert foreign key constraints that reference releases
        $this->execute('ALTER TABLE torrents_leech_stats DROP FOREIGN KEY IF EXISTS torrents_leech_stats_ibfk_1');
        $this->execute('ALTER TABLE torrents_leech_stats ADD CONSTRAINT torrents_leech_stats_ibfk_1 FOREIGN KEY (TorrentID) REFERENCES torrents (ID) ON DELETE CASCADE');
        
        $this->execute('ALTER TABLE users_torrent_history_snatch DROP FOREIGN KEY IF EXISTS users_torrent_history_snatch_ibfk_1');
        $this->execute('ALTER TABLE users_torrent_history_snatch ADD CONSTRAINT users_torrent_history_snatch_ibfk_1 FOREIGN KEY (TorrentID) REFERENCES torrents (ID) ON DELETE CASCADE');
        
        // Step 5: Revert foreign key constraints that reference releases_group
        $this->execute('ALTER TABLE requests DROP FOREIGN KEY IF EXISTS requests_ibfk_1');
        $this->execute('ALTER TABLE requests ADD CONSTRAINT requests_ibfk_1 FOREIGN KEY (GroupID) REFERENCES torrents_group (ID) ON DELETE CASCADE');
        
        $this->execute('ALTER TABLE torrents_votes DROP FOREIGN KEY IF EXISTS torrents_votes_ibfk_1');
        $this->execute('ALTER TABLE torrents_votes ADD CONSTRAINT torrents_votes_ibfk_1 FOREIGN KEY (GroupID) REFERENCES torrents_group (ID) ON DELETE CASCADE');
        
        $this->execute('ALTER TABLE users_votes DROP FOREIGN KEY IF EXISTS users_votes_ibfk_1');
        $this->execute('ALTER TABLE users_votes ADD CONSTRAINT users_votes_ibfk_1 FOREIGN KEY (GroupID) REFERENCES torrents_group (ID) ON DELETE CASCADE');
        
        $this->execute('ALTER TABLE bookmarks_torrents DROP FOREIGN KEY IF EXISTS bookmarks_torrents_ibfk_1');
        $this->execute('ALTER TABLE bookmarks_torrents ADD CONSTRAINT bookmarks_torrents_ibfk_1 FOREIGN KEY (GroupID) REFERENCES torrents_group (ID) ON DELETE CASCADE');
        
        $this->execute('ALTER TABLE collages_torrents DROP FOREIGN KEY IF EXISTS collages_torrents_ibfk_1');
        $this->execute('ALTER TABLE collages_torrents ADD CONSTRAINT collages_torrents_ibfk_1 FOREIGN KEY (GroupID) REFERENCES torrents_group (ID) ON DELETE CASCADE');
        
        $this->execute('ALTER TABLE torrents_tags DROP FOREIGN KEY IF EXISTS torrents_tags_ibfk_1');
        $this->execute('ALTER TABLE torrents_tags ADD CONSTRAINT torrents_tags_ibfk_1 FOREIGN KEY (GroupID) REFERENCES torrents_group (ID) ON DELETE CASCADE');
        
        $this->execute('ALTER TABLE torrents_artists DROP FOREIGN KEY IF EXISTS torrents_artists_ibfk_1');
        $this->execute('ALTER TABLE torrents_artists ADD CONSTRAINT torrents_artists_ibfk_1 FOREIGN KEY (GroupID) REFERENCES torrents_group (ID) ON DELETE CASCADE');
        
        // Step 6: Revert table names
        $this->execute('RENAME TABLE releases TO torrents');
        $this->execute('RENAME TABLE releases_group TO torrents_group');
    }
}