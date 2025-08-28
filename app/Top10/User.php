<?php

namespace Gazelle\Top10;

class User extends \Gazelle\Base {
    final public const UPLOADERS      = 'uploaders';
    final public const DOWNLOADERS    = 'downloaders';
    final public const UPLOADS        = 'uploads';
    final public const REQUEST_VOTES  = 'request_votes';
    final public const REQUEST_FILLS  = 'request_fills';
    final public const UPLOAD_SPEED   = 'upload_speed';
    final public const DOWNLOAD_SPEED = 'download_speed';

    private const CACHE_KEY = 'topusers_%s_%d';

    private array $sortMap = [
        self::UPLOADERS      => 'uploaded',
        self::DOWNLOADERS    => 'downloaded',
        self::UPLOADS        => 'num_uploads',
        self::REQUEST_VOTES  => 'request_votes',
        self::REQUEST_FILLS  => 'request_fills',
        self::UPLOAD_SPEED   => 'up_speed',
        self::DOWNLOAD_SPEED => 'down_speed',
    ];

    public function fetch(string $type, int $limit): array {
        // Note: Most torrent-related functionality has been removed for music catalog
        // This method is deprecated and will always return empty array
        return [];
    }
}
