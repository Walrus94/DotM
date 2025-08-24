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
            $this->execute("DROP TABLE IF EXISTS $table CASCADE");
        }
        foreach ([
            'relay.deleted_torrent_has_attr',
            'relay.deleted_torrents_files',
            'relay.deleted_torrents_leech_stats',
            'relay.deleted_torrents',
            'relay.torrent_has_attr',
            'relay.torrent_group_has_attr',
            'relay.torrent_attr',
            'relay.torrent_group_attr',
            'relay.torrent_report_configuration_log',
            'relay.torrent_report_configuration',
            'relay.torrent_unseeded_claim',
            'relay.torrent_unseeded',
            'relay.torrents_logs',
        ] as $table) {
            $this->execute("DROP FOREIGN TABLE IF EXISTS $table CASCADE");
        }
        foreach ([
            'deleted_torrents_remastered_t',
            'deleted_torrents_scene_t',
            'deleted_torrents_haslog_t',
            'deleted_torrents_hascue_t',
            'deleted_torrents_haslogdb_t',
            'deleted_torrents_logchecksum_t',
            'deleted_torrents_freetorrent_t',
            'deleted_torrents_freeleechtype_t',
            'torrent_report_configuration_need_image_t',
            'torrent_report_configuration_need_link_t',
            'torrent_report_configuration_need_sitelink_t',
            'torrent_report_configuration_need_track_t',
            'torrent_unseeded_state_t',
            'torrent_unseeded_notify_t',
            'torrents_logs_checksum_t',
            'torrents_logs_adjusted_t',
            'torrents_logs_adjustedchecksum_t',
            'torrents_logs_checksumstate_t',
        ] as $type) {
            $this->execute("DROP TYPE IF EXISTS $type");
        }
    }

    public function down(): void {
        // intentionally left blank
    }
}
