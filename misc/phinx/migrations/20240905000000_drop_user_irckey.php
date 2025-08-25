<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class DropUserIrcKey extends AbstractMigration {
    public function up(): void {
        $this->table('users_main')->removeColumn('IRCKey')->save();
    }

    public function down(): void {
        $this->table('users_main')
            ->addColumn('IRCKey', 'char', ['null' => true, 'default' => null, 'limit' => 32])
            ->save();
    }
}
