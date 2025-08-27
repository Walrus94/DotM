<?php

namespace Gazelle;

class ReleasePlatform extends BaseObject {
    final public const tableName = 'release_platform';
    final public const pkName    = 'ID';

    final public const PLATFORMS = [
        'Spotify'      => 'Spotify',
        'Apple Music'  => 'Apple Music', 
        'Bandcamp'     => 'Bandcamp',
        'SoundCloud'   => 'SoundCloud',
    ];

    public function flush(): static {
        unset($this->info);
        return $this;
    }

    public function info(): array {
        if (!isset($this->info)) {
            $this->info = self::$db->rowAssoc("
                SELECT ID, ReleaseID, Platform, Url, created, updated
                FROM release_platform 
                WHERE ID = ?
                ", $this->id
            ) ?? [];
        }
        return $this->info;
    }

    public function releaseId(): int {
        return (int)$this->info()['ReleaseID'];
    }

    public function platform(): string {
        return $this->info()['Platform'] ?? '';
    }

    public function url(): string {
        return $this->info()['Url'] ?? '';
    }

    public function created(): string {
        return $this->info()['created'] ?? '';
    }

    public function platformIcon(): string {
        return match($this->platform()) {
            'Spotify'      => 'fa-spotify',
            'Apple Music'  => 'fa-apple',
            'Bandcamp'     => 'fa-bandcamp',
            'SoundCloud'   => 'fa-soundcloud',
            default        => 'fa-external-link',
        };
    }

    public function platformColor(): string {
        return match($this->platform()) {
            'Spotify'      => '#1DB954',
            'Apple Music'  => '#FA243C',
            'Bandcamp'     => '#629AA0',
            'SoundCloud'   => '#FF5500',
            default        => '#333',
        };
    }

    public static function isValidPlatform(string $platform): bool {
        return array_key_exists($platform, self::PLATFORMS);
    }

    public static function validPlatforms(): array {
        return array_keys(self::PLATFORMS);
    }
}