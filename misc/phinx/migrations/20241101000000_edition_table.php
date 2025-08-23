<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

final class EditionTable extends AbstractMigration {
    public function up(): void {
        // rename torrents table to edition
        $this->table('torrents')
            ->rename('edition')
            ->renameColumn('ID', 'edition_id')
            ->removeColumn('Media')
            ->removeColumn('Seeders')
            ->removeColumn('Leechers')
            ->removeColumn('LogChecksum')
            ->addColumn('release_id', 'integer', ['null' => false])
            ->addForeignKey('release_id', 'release', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addColumn('edition_type', 'enum', ['values' => ['original', 'remaster'], 'default' => 'original'])
            ->removeColumn('Remastered')
            ->addIndex(['release_id'])
            ->save();
    }

    public function down(): void {
        // revert edition table back to torrents
        $this->table('edition')
            ->removeColumn('edition_type')
            ->removeColumn('release_id')
            ->addColumn('Remastered', 'string', ['limit' => 1, 'default' => '0'])
            ->addColumn('LogChecksum', 'string', ['limit' => 40, 'null' => true])
            ->addColumn('Leechers', 'integer', ['signed' => false, 'default' => 0])
            ->addColumn('Seeders', 'integer', ['signed' => false, 'default' => 0])
            ->addColumn('Media', 'string', ['limit' => 32, 'null' => true])
            ->renameColumn('edition_id', 'ID')
            ->rename('torrents')
            ->save();
    }
}
