<?php

namespace Gazelle\API;

/**
 * Request API has been disabled for music catalog.
 * All torrent-related request functionality has been removed.
 */
class Request extends AbstractAPI {
    public function run(): array {
        // Request system disabled for music catalog
        json_error('Request system has been disabled for music catalog');
        
        // This code will never execute, but keeps the method signature valid
        return [];
    }
}
