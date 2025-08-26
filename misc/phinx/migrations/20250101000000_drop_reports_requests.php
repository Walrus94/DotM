<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class DropReportsRequests extends AbstractMigration {
    public function up(): void {
        $tables = [
            'reports',
            'reportsv2',
            'requests',
            'requests_artists',
            'requests_tags',
            'requests_votes',
            'bookmarks_requests',
            'sphinx_requests',
            'sphinx_requests_delta',
            'users_enable_requests',
            'users_points_requests',
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
