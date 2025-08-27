<?php

namespace Gazelle\Manager;

class Release extends \Gazelle\BaseManager {
    final public const ID_KEY = 'zz_r_%d';

    protected const CACHE_KEY = 'release_%d';

    protected function className(): string {
        return \Gazelle\Release::class;
    }

    public function create(
        string $name,
        ?int $year,
        ?string $recordLabel,
        ?string $catalogNumber,
        ?int $releaseType,
        ?string $description,
        ?string $image,
        \Gazelle\User $user
    ): ?\Gazelle\Release {
        self::$db->prepared_query("
            INSERT INTO release
                   (Name, Year, record_label, catalog_number, release_type, WikiBody, WikiImage)
            VALUES (?,    ?,    ?,            ?,              ?,            ?,        ?)
            ", $name, $year, $recordLabel, $catalogNumber, $releaseType, $description, $image
        );
        
        $id = self::$db->inserted_id();
        if (!$id) {
            return null;
        }

        $release = $this->findById($id);
        if ($release) {
            // Log the creation
            (new \Gazelle\Manager\SiteLog())->create(
                'release_create',
                "Release '{$name}' created",
                $user->id()
            );
        }
        
        return $release;
    }

    public function findByName(string $name): ?\Gazelle\Release {
        $id = self::$db->scalar("
            SELECT ID FROM release WHERE Name = ?
            ", $name
        );
        return $id ? $this->findById($id) : null;
    }

    public function search(string $query, int $limit = 50): array {
        self::$db->prepared_query("
            SELECT r.ID,
                   r.Name,
                   r.Year,
                   GROUP_CONCAT(DISTINCT aa.Name ORDER BY aa.Name SEPARATOR ', ') AS ArtistName
            FROM release r
            LEFT JOIN release_artist ra ON (ra.GroupID = r.ID)
            LEFT JOIN artists_alias aa ON (aa.AliasID = ra.AliasID)
            WHERE r.Name LIKE CONCAT('%', ?, '%')
               OR aa.Name LIKE CONCAT('%', ?, '%')
            GROUP BY r.ID, r.Name, r.Year
            ORDER BY r.Year DESC, r.Name
            LIMIT ?
            ", $query, $query, $limit
        );
        
        $results = self::$db->to_array('ID', MYSQLI_ASSOC);
        
        // Add platform information
        if ($results) {
            $ids = array_keys($results);
            self::$db->prepared_query("
                SELECT ReleaseID, Platform, Url
                FROM release_platform
                WHERE ReleaseID IN (" . placeholders($ids) . ")
                ORDER BY Platform
                ", ...$ids
            );
            
            $platforms = [];
            while ([$rid, $platform, $url] = self::$db->next_record(MYSQLI_NUM)) {
                $platforms[$rid][] = ['Platform' => $platform, 'Url' => $url];
            }
            
            foreach ($results as $id => &$row) {
                $row['platforms'] = $platforms[$id] ?? [];
            }
        }
        
        return $results;
    }

    public function recentReleases(int $limit = 10): array {
        self::$db->prepared_query("
            SELECT r.ID,
                   r.Name,
                   r.Year,
                   r.created,
                   GROUP_CONCAT(DISTINCT aa.Name ORDER BY aa.Name SEPARATOR ', ') AS ArtistName
            FROM release r
            LEFT JOIN release_artist ra ON (ra.GroupID = r.ID)
            LEFT JOIN artists_alias aa ON (aa.AliasID = ra.AliasID)
            GROUP BY r.ID, r.Name, r.Year, r.created
            ORDER BY r.created DESC
            LIMIT ?
            ", $limit
        );
        
        return self::$db->to_array(false, MYSQLI_ASSOC);
    }

    public function showcaseReleases(int $limit = 5): array {
        self::$db->prepared_query("
            SELECT r.ID,
                   r.Name,
                   r.Year,
                   r.WikiImage,
                   GROUP_CONCAT(DISTINCT aa.Name ORDER BY aa.Name SEPARATOR ', ') AS ArtistName
            FROM release r
            LEFT JOIN release_artist ra ON (ra.GroupID = r.ID)
            LEFT JOIN artists_alias aa ON (aa.AliasID = ra.AliasID)
            WHERE r.showcase = 1
            GROUP BY r.ID, r.Name, r.Year, r.WikiImage
            ORDER BY r.updated DESC
            LIMIT ?
            ", $limit
        );
        
        return self::$db->to_array(false, MYSQLI_ASSOC);
    }

    public function totalCount(): int {
        return (int)self::$db->scalar("SELECT COUNT(*) FROM release");
    }

    public function remove(\Gazelle\Release $release, \Gazelle\User $user): bool {
        $releaseId = $release->id();
        $releaseName = $release->name();

        self::$db->begin_transaction();

        try {
            // Remove platform links
            self::$db->prepared_query("DELETE FROM release_platform WHERE ReleaseID = ?", $releaseId);
            
            // Remove artist associations
            self::$db->prepared_query("DELETE FROM release_artist WHERE GroupID = ?", $releaseId);
            
            // Remove tags
            self::$db->prepared_query("DELETE FROM release_tag WHERE release_id = ?", $releaseId);
            
            // Remove from collages
            self::$db->prepared_query("DELETE FROM collages_torrents WHERE GroupID = ?", $releaseId);
            
            // Remove editions
            self::$db->prepared_query("DELETE FROM edition WHERE release_id = ?", $releaseId);
            
            // Remove the release itself
            self::$db->prepared_query("DELETE FROM release WHERE ID = ?", $releaseId);
            
            self::$db->commit();
            
            // Log the deletion
            (new \Gazelle\Manager\SiteLog())->create(
                'release_delete',
                "Release '{$releaseName}' deleted",
                $user->id()
            );
            
            $release->flush();
            return true;
            
        } catch (\Exception $e) {
            self::$db->rollback();
            return false;
        }
    }
}