<?php

namespace Gazelle\Stats;

class Artist extends \Gazelle\BaseObject {
    final public const tableName     = '/* artist stats */';
    protected const CACHE_KEY = 'a_stats_%d';

    public function flush(): static {
        self::$cache->delete_value(sprintf(self::CACHE_KEY, $this->id));
        return $this;
    }

    public function link(): string {
        return sprintf('<a href="%s">artist %d</a>', $this->url(), $this->id());
    }

    public function location(): string {
        return 'artist.php?id=' . $this->id;
    }

    public function info(): array {
        if (isset($this->info)) {
            return $this->info;
        }
        $key  = sprintf(self::CACHE_KEY, $this->id);
        $info = self::$cache->get_value($key);
        if ($info === false) {
            $info = self::$db->rowAssoc(
                "SELECT count(DISTINCT ra.release_id) AS release_total,
                    count(DISTINCT rp.Platform)      AS platform_total
                FROM release_artist            ra
                INNER JOIN artists_alias       aa ON (ra.AliasID = aa.AliasID)
                LEFT JOIN release_platform     rp ON (rp.ReleaseID = ra.release_id)
                WHERE aa.ArtistID = ?",
                $this->id(),
            ) ?? ['release_total' => 0, 'platform_total' => 0];
            self::$cache->cache_value($key, $info, 3600);
        }
        $this->info = $info;
        return $this->info;
    }

    public function releaseTotal(): int {
        return $this->info()['release_total'];
    }

    public function platformTotal(): int {
        return $this->info()['platform_total'];
    }
}

