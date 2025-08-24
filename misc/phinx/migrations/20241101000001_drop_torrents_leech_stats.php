<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class DropTorrentsLeechStats extends AbstractMigration {
    public function up(): void {
        if ($this->hasTable('torrents_leech_stats')) {
            $this->table('torrents_leech_stats')->drop()->save();
        }
    }

    public function down(): void {
        // no-op
    }
}
