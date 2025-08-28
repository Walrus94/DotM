<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateMinimalTables extends AbstractMigration {
    public function up(): void {
        // Create minimal user_bonus table
        if (!$this->hasTable('user_bonus')) {
            $this->table('user_bonus', ['id' => false, 'primary_key' => 'user_id'])
                ->addColumn('user_id', 'integer', ['null' => false])
                ->addColumn('points', 'integer', ['default' => 0, 'null' => false])
                ->addIndex(['user_id'], ['unique' => true])
                ->create();
        }

        // Create minimal user_flt table
        if (!$this->hasTable('user_flt')) {
            $this->table('user_flt', ['id' => false, 'primary_key' => 'user_id'])
                ->addColumn('user_id', 'integer', ['null' => false])
                ->addColumn('tokens', 'integer', ['default' => 0, 'null' => false])
                ->addIndex(['user_id'], ['unique' => true])
                ->create();
        }

        // Create minimal users_leech_stats table
        if (!$this->hasTable('users_leech_stats')) {
            $this->table('users_leech_stats', ['id' => false, 'primary_key' => 'UserID'])
                ->addColumn('UserID', 'integer', ['null' => false])
                ->addColumn('Uploaded', 'biginteger', ['default' => 0, 'null' => false])
                ->addColumn('Downloaded', 'biginteger', ['default' => 0, 'null' => false])
                ->addIndex(['UserID'], ['unique' => true])
                ->create();
        }
    }

    public function down(): void {
        $this->table('user_bonus')->drop()->save();
        $this->table('user_flt')->drop()->save();
        $this->table('users_leech_stats')->drop()->save();
    }
}
