<?php

namespace Gazelle\Manager;

class ReleasePlatform extends \Gazelle\BaseManager {
    protected function className(): string {
        return \Gazelle\ReleasePlatform::class;
    }

    public function create(
        \Gazelle\Release $release,
        string $platform,
        string $url,
        \Gazelle\User $user
    ): ?\Gazelle\ReleasePlatform {
        if (!\Gazelle\ReleasePlatform::isValidPlatform($platform)) {
            return null;
        }

        // Check for duplicate
        $existing = self::$db->scalar("
            SELECT ID FROM release_platform 
            WHERE ReleaseID = ? AND Platform = ?
            ", $release->id(), $platform
        );
        
        if ($existing) {
            return null; // Platform already exists for this release
        }

        self::$db->prepared_query("
            INSERT INTO release_platform
                   (ReleaseID, Platform, Url)
            VALUES (?,         ?,        ?)
            ", $release->id(), $platform, $url
        );
        
        $id = self::$db->inserted_id();
        if (!$id) {
            return null;
        }

        // Log the creation
        (new \Gazelle\Manager\SiteLog())->create(
            'platform_add',
            "Platform '{$platform}' added to release '{$release->name()}'",
            $user->id()
        );

        return $this->findById($id);
    }

    public function findByRelease(\Gazelle\Release $release): array {
        self::$db->prepared_query("
            SELECT ID, ReleaseID, Platform, Url, created
            FROM release_platform
            WHERE ReleaseID = ?
            ORDER BY Platform
            ", $release->id()
        );
        
        return self::$db->to_array(false, MYSQLI_ASSOC);
    }

    public function update(
        \Gazelle\ReleasePlatform $platform,
        string $url,
        \Gazelle\User $user
    ): bool {
        self::$db->prepared_query("
            UPDATE release_platform SET
                Url = ?,
                updated = NOW()
            WHERE ID = ?
            ", $url, $platform->id()
        );
        
        $success = self::$db->affected_rows() > 0;
        if ($success) {
            $platform->flush();
            
            // Log the update
            (new \Gazelle\Manager\SiteLog())->create(
                'platform_update',
                "Platform '{$platform->platform()}' URL updated",
                $user->id()
            );
        }
        
        return $success;
    }

    public function remove(\Gazelle\ReleasePlatform $platform, \Gazelle\User $user): bool {
        $platformName = $platform->platform();
        
        self::$db->prepared_query("
            DELETE FROM release_platform WHERE ID = ?
            ", $platform->id()
        );
        
        $success = self::$db->affected_rows() > 0;
        if ($success) {
            // Log the removal
            (new \Gazelle\Manager\SiteLog())->create(
                'platform_remove',
                "Platform '{$platformName}' removed",
                $user->id()
            );
        }
        
        return $success;
    }

    public function platformStats(): array {
        self::$db->prepared_query("
            SELECT Platform, COUNT(*) as count
            FROM release_platform
            GROUP BY Platform
            ORDER BY count DESC
        ");
        
        return self::$db->to_array('Platform', MYSQLI_ASSOC);
    }
}