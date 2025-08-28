<?php

namespace Gazelle\Json;

/**
 * Request JSON has been disabled for music catalog.
 * All torrent-related request functionality has been removed.
 */
class Request extends \Gazelle\Json {
    public function __construct(
        protected \Gazelle\Request         $request,
        protected \Gazelle\User            $viewer,
        protected \Gazelle\User\Bookmark   $bookmark,
        protected \Gazelle\Comment\Request $commentPage,
        protected \Gazelle\Manager\User    $userMan,
    ) {}

    public function payload(): array {
        // Request system disabled for music catalog
        return [
            'disabled' => true,
            'message' => 'Request system has been disabled for music catalog'
        ];
    }
}
