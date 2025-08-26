<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class DropBonusTables extends AbstractMigration {
    public function change(): void {
        foreach ([
            'bonus_history',
            'bonus_pool_contrib',
            'contest_has_bonus_pool',
            'bonus_pool',
            'bonus_item',
            'user_bonus',
        ] as $table) {
            if ($this->hasTable($table)) {
                $this->table($table)->drop()->save();
            }
        }
        $this->execute('DROP FUNCTION IF EXISTS bonus_accrual');
    }
}
