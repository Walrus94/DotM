<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class DropCollagesTables extends AbstractMigration {
    public function up(): void {
        $this->table('collage_has_attr')->drop()->save();
        $this->table('collage_attr')->drop()->save();
        $this->table('collages_artists')->drop()->save();
        $this->table('collages_torrents')->drop()->save();
        $this->table('bookmarks_collages')->drop()->save();
        $this->table('users_collage_subs')->drop()->save();
        $this->table('collages')->drop()->save();
    }

    public function down(): void {
        // no rollback
    }
}
