<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

final class CreateReleaseTables extends AbstractMigration {
    public function up(): void {
        // Create release table - main release information (only if it doesn't exist)
        if (!$this->hasTable('release')) {
            $this->table('release', ['id' => false, 'primary_key' => 'ID'])
            ->addColumn('ID', 'integer', ['limit' => 10, 'identity' => true])
            ->addColumn('Name', 'string', ['limit' => 300, 'null' => false])
            ->addColumn('Year', 'integer', ['limit' => 4, 'null' => true, 'signed' => false])
            ->addColumn('record_label', 'string', ['limit' => 200, 'null' => true])
            ->addColumn('catalog_number', 'string', ['limit' => 100, 'null' => true])
            ->addColumn('release_type', 'integer', ['limit' => 3, 'null' => true])
            ->addColumn('WikiBody', 'text', ['null' => true])
            ->addColumn('WikiImage', 'string', ['limit' => 500, 'null' => true])
            ->addColumn('TagList', 'text', ['null' => true])
            ->addColumn('showcase', 'boolean', ['default' => 0])
            ->addColumn('created', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
            ->addIndex(['Name'], ['name' => 'idx_release_name'])
            ->addIndex(['Year'], ['name' => 'idx_release_year'])
            ->addIndex(['record_label'], ['name' => 'idx_release_label'])
            ->addIndex(['created'], ['name' => 'idx_release_created'])
            ->addForeignKey('release_type', 'release_type', 'ID', ['delete' => 'SET_NULL', 'update' => 'CASCADE'])
            ->create();
        }

        // Create release_platform table - streaming platform links (only if it doesn't exist)
        if (!$this->hasTable('release_platform')) {
            $this->table('release_platform', ['id' => false, 'primary_key' => 'ID'])
            ->addColumn('ID', 'integer', ['limit' => 10, 'identity' => true])
            ->addColumn('ReleaseID', 'integer', ['limit' => 10, 'null' => false])
            ->addColumn('Platform', 'enum', [
                'values' => ['Spotify', 'Apple Music', 'Bandcamp', 'SoundCloud'],
                'null' => false
            ])
            ->addColumn('Url', 'string', ['limit' => 500, 'null' => false])
            ->addColumn('created', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
            ->addIndex(['ReleaseID'], ['name' => 'idx_platform_release'])
            ->addIndex(['Platform'], ['name' => 'idx_platform_type'])
            ->addForeignKey('ReleaseID', 'release', 'ID', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();
        }

        // Create release_artist table - artist credits for releases
        if (!$this->hasTable('release_artist')) {
            $this->table('release_artist', ['id' => false, 'primary_key' => ['GroupID', 'AliasID', 'artist_role_id']])
            ->addColumn('GroupID', 'integer', ['limit' => 10, 'null' => false])
            ->addColumn('AliasID', 'integer', ['limit' => 10, 'null' => false])
            ->addColumn('UserID', 'integer', ['limit' => 10, 'null' => false])
            ->addColumn('Importance', 'integer', ['limit' => 2, 'null' => false, 'default' => 1])
            ->addColumn('artist_role_id', 'integer', ['limit' => MysqlAdapter::INT_TINY, 'null' => false, 'default' => 1])
            ->addColumn('created', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addIndex(['GroupID'], ['name' => 'idx_release_artist_group'])
            ->addIndex(['AliasID'], ['name' => 'idx_release_artist_alias'])
            ->addForeignKey('GroupID', 'release', 'ID', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('AliasID', 'artists_alias', 'AliasID', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('artist_role_id', 'artist_role', 'artist_role_id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('UserID', 'users_main', 'ID', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();
        }

        // Create release_tag table - tags for releases
        if (!$this->hasTable('release_tag')) {
            $this->table('release_tag', ['id' => false, 'primary_key' => ['TagID', 'release_id']])
            ->addColumn('TagID', 'integer', ['limit' => 10, 'null' => false])
            ->addColumn('release_id', 'integer', ['limit' => 10, 'null' => false])
            ->addColumn('PositiveVotes', 'integer', ['limit' => 6, 'null' => false, 'default' => 1])
            ->addColumn('NegativeVotes', 'integer', ['limit' => 6, 'null' => false, 'default' => 0])
            ->addColumn('UserID', 'integer', ['limit' => 10, 'null' => false])
            ->addIndex(['release_id'], ['name' => 'idx_release_tag_release'])
            ->addIndex(['TagID'], ['name' => 'idx_release_tag_tag'])
            ->addForeignKey('release_id', 'release', 'ID', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('TagID', 'tags', 'ID', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('UserID', 'users_main', 'ID', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();
        }

        // Create edition table - different formats/editions of releases (replaces torrents concept)
        if (!$this->hasTable('edition')) {
            $this->table('edition', ['id' => false, 'primary_key' => 'edition_id'])
            ->addColumn('edition_id', 'integer', ['limit' => 10, 'identity' => true])
            ->addColumn('release_id', 'integer', ['limit' => 10, 'null' => false])
            ->addColumn('UserID', 'integer', ['limit' => 10, 'null' => false])
            ->addColumn('Format', 'string', ['limit' => 20, 'null' => true])
            ->addColumn('Encoding', 'string', ['limit' => 20, 'null' => true])
            ->addColumn('edition_type', 'enum', ['values' => ['original', 'remaster'], 'default' => 'original'])
            ->addColumn('RemasterYear', 'integer', ['limit' => 4, 'null' => true, 'signed' => false])
            ->addColumn('RemasterTitle', 'string', ['limit' => 300, 'null' => true])
            ->addColumn('RemasterCatalogueNumber', 'string', ['limit' => 100, 'null' => true])
            ->addColumn('RemasterRecordLabel', 'string', ['limit' => 200, 'null' => true])
            ->addColumn('FileList', 'mediumtext', ['null' => true])
            ->addColumn('Size', 'biginteger', ['null' => true, 'signed' => false])
            ->addColumn('Description', 'text', ['null' => true])
            ->addColumn('created', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
            ->addIndex(['release_id'], ['name' => 'idx_edition_release'])
            ->addIndex(['created'], ['name' => 'idx_edition_created'])
            ->addForeignKey('release_id', 'release', 'ID', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('UserID', 'users_main', 'ID', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();
        }
    }

    public function down(): void {
        $this->table('edition')->drop()->update();
        $this->table('release_tag')->drop()->update();
        $this->table('release_artist')->drop()->update();
        $this->table('release_platform')->drop()->update();
        $this->table('release')->drop()->update();
    }
}