<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class DropReportsRequests extends AbstractMigration {
    public function up(): void {
        $tables = [
            // drop child tables before parents to satisfy foreign key constraints
            'reportsv2',
            'reports',
            'requests_votes',
            'requests_artists',
            'requests_tags',
            'bookmarks_requests',
            'sphinx_requests',
            'sphinx_requests_delta',
            'users_enable_requests',
            'users_points_requests',
            'requests',
        ];
        foreach ($tables as $table) {
            if ($this->hasTable($table)) {
                $this->table($table)->drop()->save();
            }
        }
    }

    public function down(): void {
        // no-op
    }
}
