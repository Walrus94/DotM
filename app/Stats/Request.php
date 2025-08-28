<?php

namespace Gazelle\Stats;

/**
 * Request Stats have been disabled for music catalog.
 * All torrent-related request functionality has been removed.
 */
class Request extends \Gazelle\Base {
    protected const CACHE_KEY = 'stats_req';

    protected array $info;

    public function flush(): static {
        // Request system disabled for music catalog
        return $this;
    }

    public function info(): array {
        // Request system disabled for music catalog
        return [
            'total' => 0,
            'filled' => 0
        ];
    }

    public function total(): int {
        // Request system disabled for music catalog
        return 0;
    }

    public function filledTotal(): int {
        // Request system disabled for music catalog
        return 0;
    }

    public function filledPercent(): float {
        // Request system disabled for music catalog
        return 0.0;
    }
}
