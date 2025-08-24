<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class ReleasePlatform extends AbstractMigration {
    public function up(): void {
        $this->table('release_platform', ['id' => false, 'primary_key' => 'ID'])
            ->addColumn('ID', 'integer', ['limit' => 10, 'identity' => true])
            ->addColumn('ReleaseID', 'integer', ['limit' => 10])
            ->addColumn('Platform', 'enum', ['values' => [
                'Spotify',
                'Apple Music',
                'Bandcamp',
                'SoundCloud',
                'YouTube',
            ]])
            ->addColumn('Url', 'string', ['limit' => 255])
            ->addColumn('created', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
            ->addIndex(['ReleaseID'])
            ->addForeignKey('ReleaseID', 'torrents_group', 'ID', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();
    }

    public function down(): void {
        $this->table('release_platform')->drop()->save();
    }
}
