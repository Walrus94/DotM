<?php

namespace Gazelle;

class ReleasePlatform extends BaseObject {
    final public const tableName = 'release_platform';

    public function flush(): static {
        $this->info = [];
        return $this;
    }

    public function link(): string {
        return $this->url();
    }

    public function location(): string {
        return $this->url();
    }

    protected function info(): array {
        if (!isset($this->info) || !$this->info) {
            $this->info = self::$db->row(
                "SELECT ReleaseID, Platform, Url FROM release_platform WHERE ID = ?",
                $this->id
            ) ?? [];
        }
        return $this->info;
    }

    public function releaseId(): int {
        return (int)$this->info()['ReleaseID'];
    }

    public function platform(): string {
        return $this->info()['Platform'];
    }

    public function url(): string {
        return $this->info()['Url'];
    }

    public function update(string $platform, string $url): int {
        self::$db->prepared_query(
            "UPDATE release_platform SET Platform = ?, Url = ?, updated = NOW() WHERE ID = ?",
            $platform, $url, $this->id
        );
        $this->flush();
        return self::$db->affected_rows();
    }

    public function remove(): int {
        self::$db->prepared_query(
            "DELETE FROM release_platform WHERE ID = ?",
            $this->id
        );
        return self::$db->affected_rows();
    }
}
