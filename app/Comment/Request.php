<?php

namespace Gazelle\Comment;

/**
 * Request Comments have been disabled for music catalog.
 * All torrent-related request functionality has been removed.
 */
class Request extends AbstractComment {
    public function page(): string {
        // Request system disabled for music catalog
        return 'disabled';
    }

    public function pageUrl(): string {
        // Request system disabled for music catalog
        return "disabled";
    }
}
