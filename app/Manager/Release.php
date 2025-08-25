<?php

namespace Gazelle\Manager;

class Release extends \Gazelle\BaseManager {
    final public const ID_KEY = 'zz_rl_%d';

    protected \Gazelle\User $viewer;

    public function create(
        string  $name,
        string  $description,
        ?int    $year,
        ?int    $releaseType,
        ?string $recordLabel,
        ?string $catalogNumber,
        ?string $image,
        string  $tagList = '',
        bool    $showcase = false,
    ): \Gazelle\Release {
        self::$db->prepared_query(
            "INSERT INTO release
                   (Name, WikiBody, Year, record_label, catalog_number, WikiImage, TagList, release_type, showcase)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
            $name, $description, $year, $recordLabel, $catalogNumber, $image, $tagList, $releaseType, $showcase ? 1 : 0
        );
        $id = self::$db->inserted_id();
        return $this->findById((int)$id);
    }

    public function findById(int $id): ?\Gazelle\Release {
        $key = sprintf(self::ID_KEY, $id);
        $found = self::$cache->get_value($key);
        if ($found === false) {
            $found = self::$db->scalar(
                'SELECT ID FROM release WHERE ID = ?',
                $id
            );
            if (!is_null($found)) {
                self::$cache->cache_value($key, $found, 7200);
            }
        }
        if (!$found) {
            return null;
        }
        $release = new \Gazelle\Release($found);
        if (isset($this->viewer)) {
            $release->setViewer($this->viewer);
        }
        return $release;
    }

    public function setViewer(\Gazelle\User $viewer): static {
        $this->viewer = $viewer;
        return $this;
    }
}
