<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class ReleaseTable extends AbstractMigration {
    public function up(): void {
        // rename torrents_group table to release if needed
        if ($this->hasTable('torrents_group') && !$this->hasTable('release')) {
            $this->table('torrents_group')->rename('release')->save();
        }

        if (!$this->hasTable('release')) {
            // nothing to do if neither table exists
            return;
        }

        // adjust column set
        $table = $this->table('release');
        $needsUpdate = false;
        foreach (['CategoryID', 'Time', 'RevisionID', 'VanityHouse'] as $col) {
            if ($table->hasColumn($col)) {
                $table->removeColumn($col);
                $needsUpdate = true;
            }
        }
        if ($table->hasColumn('CatalogueNumber') && !$table->hasColumn('catalog_number')) {
            $table->renameColumn('CatalogueNumber', 'catalog_number');
            $needsUpdate = true;
        }
        if ($table->hasColumn('RecordLabel') && !$table->hasColumn('record_label')) {
            $table->renameColumn('RecordLabel', 'record_label');
            $needsUpdate = true;
        }
        if ($table->hasColumn('ReleaseType') && !$table->hasColumn('release_type')) {
            $table->renameColumn('ReleaseType', 'release_type');
            $needsUpdate = true;
        }
        if (!$table->hasColumn('showcase')) {
            $table->addColumn('showcase', 'boolean', ['default' => 0, 'null' => false]);
            $needsUpdate = true;
        }
        if ($needsUpdate) {
            $table->update();
        }

        // update foreign keys
        $fkOptions = ['delete' => 'CASCADE', 'update' => 'CASCADE'];
        foreach (['torrents', 'torrents_artists', 'bookmarks_torrents', 'collages_torrents', 'requests'] as $name) {
            $table = $this->table($name);
            if ($table->hasForeignKey('GroupID')) {
                $table->dropForeignKey('GroupID');
            }
            $table->addForeignKey('GroupID', 'release', 'ID', $fkOptions)->update();
        }
    }

    public function down(): void {
        // rename table back before adjusting columns or foreign keys
        if ($this->hasTable('release') && !$this->hasTable('torrents_group')) {
            $this->table('release')->rename('torrents_group')->save();
        }
        if (!$this->hasTable('torrents_group')) {
            return;
        }

        // restore column set
        $table = $this->table('torrents_group');
        $needsUpdate = false;
        if ($table->hasColumn('showcase')) {
            $table->removeColumn('showcase');
            $needsUpdate = true;
        }
        if ($table->hasColumn('catalog_number') && !$table->hasColumn('CatalogueNumber')) {
            $table->renameColumn('catalog_number', 'CatalogueNumber');
            $needsUpdate = true;
        }
        if ($table->hasColumn('record_label') && !$table->hasColumn('RecordLabel')) {
            $table->renameColumn('record_label', 'RecordLabel');
            $needsUpdate = true;
        }
        if ($table->hasColumn('release_type') && !$table->hasColumn('ReleaseType')) {
            $table->renameColumn('release_type', 'ReleaseType');
            $needsUpdate = true;
        }
        foreach (['CategoryID', 'Time', 'RevisionID', 'VanityHouse'] as $col) {
            if (!$table->hasColumn($col)) {
                switch ($col) {
                    case 'CategoryID':
                        $table->addColumn('CategoryID', 'integer', ['limit' => 3, 'default' => null, 'null' => true]);
                        break;
                    case 'Time':
                        $table->addColumn('Time', 'datetime', ['null' => true]);
                        break;
                    case 'RevisionID':
                        $table->addColumn('RevisionID', 'integer', ['limit' => 12, 'default' => null, 'null' => true]);
                        break;
                    case 'VanityHouse':
                        $table->addColumn('VanityHouse', 'boolean', ['default' => 0, 'null' => true]);
                        break;
                }
                $needsUpdate = true;
            }
        }
        if ($needsUpdate) {
            $table->update();
        }

        // revert foreign keys
        $fkOptions = ['delete' => 'CASCADE', 'update' => 'CASCADE'];
        foreach (['torrents', 'torrents_artists', 'bookmarks_torrents', 'collages_torrents', 'requests'] as $name) {
            $table = $this->table($name);
            if ($table->hasForeignKey('GroupID')) {
                $table->dropForeignKey('GroupID');
            }
            $table->addForeignKey('GroupID', 'torrents_group', 'ID', $fkOptions)->update();
        }
    }
}
