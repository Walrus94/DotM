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
        $key = sprintf(self::CACHE_KEY, $this->id);
        $info = self::$cache->get_value($key);
        if ($info === false) {
            $info = self::$db->rowAssoc(
                "SELECT count(DISTINCT ra.release_id) AS release_total,
                    count(DISTINCT rp.Platform)      AS platform_total
                FROM release_artist            ra
                INNER JOIN artists_alias       aa ON (ra.AliasID = aa.AliasID)
                LEFT JOIN release_platform     rp ON (rp.ReleaseID = ra.release_id)
                WHERE aa.ArtistID = ?",
                $this->id()
            ) ?? ['release_total' => 0, 'platform_total' => 0];
            $info['tgroup_total']  = $info['release_total'];
            $info['torrent_total'] = $info['release_total'];
            $info['leecher_total'] = 0;
            $info['seeder_total']  = 0;
            $info['snatch_total']  = 0;
            self::$cache->cache_value($key, $info, 3600);
        }
        $this->info = $info;
        return $this->info;
    }

    public function leecherTotal(): int {
        return $this->info()['leecher_total'];
    }

    public function seederTotal(): int {
        return $this->info()['seeder_total'];
    }

    public function snatchTotal(): int {
        return $this->info()['snatch_total'];
    }

    public function releaseTotal(): int {
        return $this->info()['release_total'];
    }

    public function platformTotal(): int {
        return $this->info()['platform_total'];
    }

    public function tgroupTotal(): int {
        return $this->info()['tgroup_total'];
    }

    public function torrentTotal(): int {
        return $this->info()['torrent_total'];
    }
}

