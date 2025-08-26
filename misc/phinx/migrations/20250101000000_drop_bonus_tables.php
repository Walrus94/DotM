<?php
use Phinx\Migration\AbstractMigration;

final class DropBonusTables extends AbstractMigration {
    public function change(): void {
        foreach (['bonus_history', 'bonus_item', 'bonus_pool', 'bonus_pool_contrib', 'user_bonus', 'contest_has_bonus_pool'] as $table) {
            if ($this->hasTable($table)) {
                $this->table($table)->drop()->save();
            }
        }
        $this->execute('DROP FUNCTION IF EXISTS bonus_accrual');
    }
}
