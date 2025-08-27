<?php

namespace Gazelle;

/**
 * TGroup is now an alias for Release to maintain backward compatibility
 * during the transition from BitTorrent tracker to music catalog
 */
class TGroup extends Release {
    final public const tableName = 'release'; // Updated to use release table
    
    // Legacy compatibility methods
    public function groupIds(): array {
        return [$this->id()]; // Single release instead of torrent group
    }

    public function sections(): array {
        // Return single section for compatibility
        return [1 => [$this->id()]];
    }

    public function torrentLink(int $torrentId): string {
        // Redirect to release page instead of torrent
        return $this->link();
    }

    public function torrentIdList(): array {
        // No torrents anymore, return empty
        return [];
    }

    // Preserve artist functionality that's used throughout the codebase
    public function artistName(): ?string {
        return $this->artistRole()?->text();
    }
}