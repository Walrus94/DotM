<?php

use Phinx\Migration\AbstractMigration;

/**
 * Drop all request-related tables for music catalog.
 * These tables were used for torrent request functionality and are not needed.
 */
class DropRequestTables extends AbstractMigration {
    public function up(): void {
        // Disable foreign key checks to avoid constraint issues
        $this->execute('SET FOREIGN_KEY_CHECKS = 0');
        
        // Drop request-related tables
        $requestTables = [
            'requests',
            'requests_votes', 
            'requests_artists',
            'requests_tags',
            'requests_comments',
            'requests_log',
            'requests_fill',
            'requests_unfill',
            'requests_delete',
            'requests_edit',
            'requests_bounty',
            'requests_fill_handle',
            'requests_unfill_handle',
            'requests_delete_handle',
            'requests_edit_handle',
            'requests_bounty_handle'
        ];
        
        foreach ($requestTables as $table) {
            if ($this->hasTable($table)) {
                $this->table($table)->drop()->save();
            }
        }
        
        // Re-enable foreign key checks
        $this->execute('SET FOREIGN_KEY_CHECKS = 1');
    }

    public function down(): void {
        // This migration cannot be safely reversed
        // The request system has been permanently disabled for music catalog
        throw new \Exception('Request system has been permanently disabled for music catalog');
    }
}
