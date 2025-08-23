<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class EditionTable extends AbstractMigration {
    public function up(): void {
        // rename torrents table to edition if needed
        if (!$this->hasTable('edition') && $this->hasTable('torrents')) {
            $this->table('torrents')->rename('edition')->save();
        }

        if (!$this->hasTable('edition')) {
            return; // nothing to do
        }

        $table = $this->table('edition');
        if ($table->hasColumn('ID')) {
            $table->renameColumn('ID', 'edition_id');
        }
        foreach (['Media', 'Seeders', 'Leechers', 'LogChecksum', 'Remastered'] as $col) {
            if ($table->hasColumn($col)) {
                $table->removeColumn($col);
            }
        }

        if (!$table->hasColumn('release_id')) {
            $table
                ->addColumn('release_id', 'integer', ['null' => false])
                ->addForeignKey('release_id', 'release', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
                ->addIndex(['release_id']);
        }

        if (!$table->hasColumn('edition_type')) {
            $table->addColumn('edition_type', 'enum', ['values' => ['original', 'remaster'], 'default' => 'original']);
        }

        $table->save();
    }

    public function down(): void {
        // revert edition table back to torrents
        $table = $this->table('edition');
        $table
            ->removeColumn('edition_type')
            ->removeColumn('release_id')
            ->addColumn('Remastered', 'string', ['limit' => 1, 'default' => '0'])
            ->addColumn('LogChecksum', 'string', ['limit' => 40, 'null' => true])
            ->addColumn('Leechers', 'integer', ['signed' => false, 'default' => 0])
            ->addColumn('Seeders', 'integer', ['signed' => false, 'default' => 0])
            ->addColumn('Media', 'string', ['limit' => 32, 'null' => true]);
        if ($table->hasColumn('edition_id')) {
            $table->renameColumn('edition_id', 'ID');
        }
        $table->rename('torrents')->save();
    }
}
