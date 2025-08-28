<?php

namespace Gazelle\ArtistRole;

/**
 * Request Artist Roles have been disabled for music catalog.
 * All torrent-related request functionality has been removed.
 */
class Request extends \Gazelle\ArtistRole {
    /**
     * Create or modify the set of artists associated with a request
     */
    public function set(array $roleList, \Gazelle\User $user, \Gazelle\Manager\Artist $manager): int {
        // Request system disabled for music catalog
        return 0;
    }

    protected function artistListQuery(): \mysqli_result|bool {
        // Request system disabled for music catalog
        return false;
    }

    /**
     * A cryptic representation of the artists grouped by their roles in a
     * release group. All artist roles are present as arrays (no need to see if
     * the key exists).
     * A role is an array of three keys: ["id" => 801, "aliasid" => 768, "name" => "The Group"]
     */
    public function idList(): array {
        // Request system disabled for music catalog
        return [];
    }

    public function roleNameList(): array {
        // Request system disabled for music catalog
        return [];
    }

    public function nameList(): array {
        // Request system disabled for music catalog
        return [];
    }

    public function roleList(): array {
        // Request system disabled for music catalog
        return [];
    }
}
