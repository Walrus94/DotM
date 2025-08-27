<?php

use Phinx\Migration\AbstractMigration;

final class DropBittorrentTables extends AbstractMigration {
    public function up(): void {
        // Drop torrent-related tables
        $tables_to_drop = [
            // Core torrent tables
            'torrents',
            'torrents_group', 
            'torrents_artists',
            'torrents_tags',
            'torrents_tags_votes',
            'torrents_votes',
            'torrents_peerlists',
            'torrents_peerlists_compare',
            
            // Bonus system tables
            'bonus_history',
            'bonus_item',
            'bonus_pool',
            'bonus_pool_contrib',
            'user_bonus',
            'user_flt',
            
            // BitTorrent-specific user tables
            'users_freeleeches',
            'users_downloads',
            'users_leech_stats',
            'user_torrent_remove',
            
            // Tracker tables (XBT)
            'xbt_client_whitelist',
            'xbt_files_history', 
            'xbt_files_users',
            'xbt_forex',
            'xbt_snatched',
            
            // Torrent statistics and tracking
            'tgroup_summary',
            'top10_history_torrents',
            'users_torrent_history',
            'ratelimit_torrent',
            
            // Better/transcoding system
            'better_transcode_music',
            
            // Torrent-specific notifications
            'users_notify_torrents',
            'deleted_users_notify_torrents',
            
            // Deleted torrent tracking
            'deleted_torrents_group',
            
            // Bookmarks (will be replaced with release bookmarks)
            'bookmarks_torrents',
            
            // Collage torrents (will be replaced with release collages)
            'collages_torrents',
        ];

        foreach ($tables_to_drop as $table) {
            if ($this->hasTable($table)) {
                $this->table($table)->drop()->update();
            }
        }
    }

    public function down(): void {
        // This migration is intentionally irreversible for safety
        // The old torrent structure would be complex to recreate
        // and we're committed to the music catalog direction
        throw new \Exception('This migration cannot be reversed - BitTorrent functionality removal is permanent');
    }
}