<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class DropLegacyTorrentTables extends AbstractMigration {
    public function up(): void {
        foreach ([
            'deleted_torrent_has_attr',
            'deleted_torrents_files',
            'deleted_torrents_leech_stats',
            'deleted_torrents',
            'torrent_has_attr',
            'torrent_group_has_attr',
            'torrent_attr',
            'torrent_group_attr',
            'torrent_report_configuration_log',
            'torrent_report_configuration',
            'torrent_unseeded_claim',
            'torrent_unseeded',
            'torrents_logs',
        ] as $table) {
            if ($this->hasTable($table)) {
                $this->table($table)->drop()->save();
            }
        }
    }

    public function down(): void {
        // intentionally left blank
    }
}
