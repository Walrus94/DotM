<?php

namespace Gazelle\Manager;

/**
 * Request Manager has been disabled for music catalog.
 * All torrent-related request functionality has been removed.
 */
class Request extends \Gazelle\BaseManager {
    final public const ID_KEY = 'zz_r_%d';

    public function create(
        \Gazelle\User $user,
        int $bounty,
        int $categoryId,
        int $year,
        string $title,
        ?string $image,
        string $description,
        string $recordLabel,
        string $catalogueNumber,
        int $releaseType,
        string $encodingList,
        string $formatList,
        string $mediaList,
        string $logCue,
        bool $checksum,
        string $oclc,
        int|null $groupId = null,
    ): \Gazelle\Request {
        // Request system disabled for music catalog
        throw new \Exception('Request system disabled for music catalog');
    }

    public function findById(int $requestId): ?\Gazelle\Request {
        // Request system disabled for music catalog
        return null;
    }

    /**
     * Find a list of unfilled requests by a user, sorted
     * by most number of votes and then largest bounty
     *
     * @return array of \Gazelle\Request objects
     */
    public function findUnfilledByUser(\Gazelle\User $user, int $limit): array {
        // Request system disabled for music catalog
        return [];
    }

    public function findByArtist(\Gazelle\Artist $artist): array {
        // Request system disabled for music catalog
        return [];
    }

    public function findByTGroup(\Gazelle\TGroup $tgroup): array {
        // Request system disabled for music catalog
        return [];
    }

    public function findByTorrentReported(\Gazelle\TorrentAbstract $torrent): array {
        // Request system disabled for music catalog
        return [];
    }
}
