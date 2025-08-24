<?php

namespace Gazelle\Manager;

class ReleasePlatform extends \Gazelle\BaseManager {
    public function findById(int $id): ?\Gazelle\ReleasePlatform {
        $found = self::$db->scalar("SELECT 1 FROM release_platform WHERE ID = ?", $id);
        if (!$found) {
            return null;
        }
        return new \Gazelle\ReleasePlatform($id);
    }

    public function create(int $releaseId, string $platform, string $url): \Gazelle\ReleasePlatform {
        self::$db->prepared_query(
            "INSERT INTO release_platform (ReleaseID, Platform, Url) VALUES (?, ?, ?)",
            $releaseId, $platform, $url
        );
        return $this->findById(self::$db->inserted_id());
    }

    public function listByRelease(int $releaseId): array {
        self::$db->prepared_query(
            "SELECT ID, Platform, Url FROM release_platform WHERE ReleaseID = ? ORDER BY ID",
            $releaseId
        );
        return self::$db->to_array(false, MYSQLI_ASSOC, false);
    }

    public function update(int $id, string $platform, string $url): int {
        self::$db->prepared_query(
            "UPDATE release_platform SET Platform = ?, Url = ?, updated = NOW() WHERE ID = ?",
            $platform, $url, $id
        );
        return self::$db->affected_rows();
    }

    public function remove(int $id): int {
        self::$db->prepared_query("DELETE FROM release_platform WHERE ID = ?", $id);
        return self::$db->affected_rows();
    }

    public function removeForRelease(int $releaseId): int {
        self::$db->prepared_query("DELETE FROM release_platform WHERE ReleaseID = ?", $releaseId);
        return self::$db->affected_rows();
    }
}
