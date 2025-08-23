<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class ReleaseTable extends AbstractMigration {
    public function up(): void {
        // rename torrents_group table to release
        $this->table('torrents_group')->rename('release')->save();

        // adjust column set
        $this->table('release')
            ->removeColumn('CategoryID')
            ->removeColumn('Time')
            ->removeColumn('RevisionID')
            ->removeColumn('VanityHouse')
            ->renameColumn('CatalogueNumber', 'catalog_number')
            ->renameColumn('RecordLabel', 'record_label')
            ->renameColumn('ReleaseType', 'release_type')
            ->addColumn('showcase', 'boolean', ['default' => 0, 'null' => false])
            ->update();

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
        // revert foreign keys first
        $fkOptions = ['delete' => 'CASCADE', 'update' => 'CASCADE'];
        foreach (['torrents', 'torrents_artists', 'bookmarks_torrents', 'collages_torrents', 'requests'] as $name) {
            $table = $this->table($name);
            if ($table->hasForeignKey('GroupID')) {
                $table->dropForeignKey('GroupID');
            }
            $table->addForeignKey('GroupID', 'torrents_group', 'ID', $fkOptions)->update();
        }

        // restore column set
        $this->table('release')
            ->removeColumn('showcase')
            ->renameColumn('catalog_number', 'CatalogueNumber')
            ->renameColumn('record_label', 'RecordLabel')
            ->renameColumn('release_type', 'ReleaseType')
            ->addColumn('CategoryID', 'integer', ['limit' => 3, 'default' => null, 'null' => true])
            ->addColumn('Time', 'datetime', ['null' => true])
            ->addColumn('RevisionID', 'integer', ['limit' => 12, 'default' => null, 'null' => true])
            ->addColumn('VanityHouse', 'boolean', ['default' => 0, 'null' => true])
            ->update();

        // rename table back
        $this->table('release')->rename('torrents_group')->save();
    }
}
