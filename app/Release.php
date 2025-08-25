<?php

namespace Gazelle;

class Release extends BaseObject {
    final public const tableName = 'release';
    final public const pkName    = 'ID';

    protected const CACHE_KEY = 'release_%d';

    public function flush(): static {
        unset($this->info);
        self::$cache->delete_value(sprintf(self::CACHE_KEY, $this->id));
        return $this;
    }

    public function link(): string {
        return sprintf('<a href="%s">%s</a>', $this->url(), display_str($this->name()));
    }

    public function location(): string {
        return 'releases.php?releaseid=' . $this->id;
    }

    public function info(): array {
        if (!isset($this->info)) {
            $this->info = self::$db->rowAssoc(
                "SELECT ID, Name, Year, record_label, catalog_number, WikiBody, WikiImage, TagList, release_type, showcase
                 FROM `release` WHERE ID = ?",
                $this->id
            ) ?? [];
        }
        return $this->info;
    }

    public function name(): string {
        return $this->info()['Name'] ?? '';
    }

    public function year(): ?int {
        return isset($this->info()['Year']) ? (int)$this->info()['Year'] : null;
    }

    public function recordLabel(): string {
        return $this->info()['record_label'] ?? '';
    }

    public function catalogNumber(): string {
        return $this->info()['catalog_number'] ?? '';
    }

    public function description(): string {
        return $this->info()['WikiBody'] ?? '';
    }

    public function image(): string {
        return $this->info()['WikiImage'] ?? '';
    }

    public function showcase(): bool {
        return !empty($this->info()['showcase']);
    }

    public function tagList(): array {
        $list = $this->info()['TagList'] ?? '';
        return $list === '' ? [] : explode(' ', $list);
    }
}
