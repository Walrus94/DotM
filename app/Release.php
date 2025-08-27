<?php

namespace Gazelle;

use Gazelle\Intf\CategoryHasArtist;
use Gazelle\Intf\CollageEntry;

class Release extends BaseObject implements CategoryHasArtist, CollageEntry {
    final public const tableName    = 'release';
    final public const pkName       = 'ID';
    final public const CACHE_KEY    = 'release_%d';

    protected ArtistRole\TGroup $artistRole;
    protected User              $viewer;

    public function flush(): static {
        unset($this->info);
        unset($this->artistRole);
        self::$cache->delete_value(sprintf(self::CACHE_KEY, $this->id));
        return $this;
    }

    public function link(): string {
        return sprintf('<a href="%s" title="View release" dir="ltr">%s</a>', 
            $this->url(), 
            display_str($this->name())
        );
    }

    public function location(): string {
        return "releases.php?id=" . $this->id;
    }

    public function url(): string {
        return site_url() . "/" . $this->location();
    }

    public function text(): string {
        $text = $this->hasArtistRole() 
            ? "{$this->artistRole()->text()} – {$this->name()}"
            : $this->name();
        
        if ($this->year()) {
            $text .= " [{$this->year()}";
            if ($this->releaseTypeName()) {
                $text .= " {$this->releaseTypeName()}";
            }
            $text .= "]";
        }
        
        return $text . ($this->isShowcase() ? ' [Showcase]' : '');
    }

    public function setViewer(User $viewer): static {
        $this->viewer = $viewer;
        return $this;
    }

    /**
     * Get the metadata of the release
     */
    public function info(): array {
        if (isset($this->info)) {
            return $this->info;
        }
        
        $key = sprintf(self::CACHE_KEY, $this->id);
        $info = self::$cache->get_value($key);
        
        if ($info === false) {
            $info = self::$db->rowAssoc("
                SELECT r.ID,
                       r.Name,
                       r.Year,
                       r.record_label,
                       r.catalog_number,
                       r.release_type,
                       r.WikiBody,
                       r.WikiImage,
                       r.TagList,
                       r.showcase,
                       r.created,
                       r.updated,
                       GROUP_CONCAT(DISTINCT t.Name ORDER BY rt.PositiveVotes - rt.NegativeVotes DESC, t.Name) AS tagNames,
                       GROUP_CONCAT(DISTINCT t.ID ORDER BY rt.PositiveVotes - rt.NegativeVotes DESC, t.Name) AS tagIds
                FROM release r
                LEFT JOIN release_tag rt ON (rt.release_id = r.ID)
                LEFT JOIN tags t ON (t.ID = rt.TagID)
                WHERE r.ID = ?
                GROUP BY r.ID
                ", $this->id
            ) ?? [];
            
            if (empty($info)) {
                return $this->info = [];
            }

            // Process tags
            if ($info['tagNames']) {
                $tagNames = explode(',', $info['tagNames']);
                $tagIds = array_map('intval', explode(',', $info['tagIds']));
                $info['tags'] = [];
                for ($i = 0; $i < count($tagIds); $i++) {
                    $info['tags'][$tagIds[$i]] = [
                        'name' => $tagNames[$i],
                        'id' => $tagIds[$i]
                    ];
                }
            } else {
                $info['tags'] = [];
            }
            
            // Clean up booleans
            $info['showcase'] = (bool)$info['showcase'];
            
            self::$cache->cache_value($key, $info, 0);
        }
        
        $this->info = $info;
        return $this->info;
    }

    public function artistRole(): ?ArtistRole\TGroup {
        if (!isset($this->artistRole)) {
            $this->artistRole = new ArtistRole\TGroup($this, new Manager\Artist());
        }
        return $this->artistRole;
    }

    public function hasArtistRole(): bool {
        return $this->artistRole() instanceof ArtistRole\TGroup;
    }

    public function name(): string {
        return $this->info()['Name'] ?? '';
    }

    public function year(): ?int {
        return isset($this->info()['Year']) ? (int)$this->info()['Year'] : null;
    }

    public function recordLabel(): ?string {
        return $this->info()['record_label'];
    }

    public function catalogNumber(): ?string {
        return $this->info()['catalog_number'];
    }

    public function releaseType(): ?int {
        return $this->info()['release_type'] ? (int)$this->info()['release_type'] : null;
    }

    public function releaseTypeName(): ?string {
        static $releaseTypes;
        if (is_null($releaseTypes)) {
            $releaseTypes = (new ReleaseType())->list();
        }
        return $this->releaseType() ? $releaseTypes[$this->releaseType()] : null;
    }

    public function description(): string {
        return $this->info()['WikiBody'] ?? '';
    }

    public function image(): ?string {
        return $this->info()['WikiImage'];
    }

    public function isShowcase(): bool {
        return $this->info()['showcase'] ?? false;
    }

    public function tagList(): array {
        return $this->info()['tags'] ?? [];
    }

    public function tagNameList(): array {
        return array_map(fn($t) => $t['name'], $this->tagList());
    }

    public function primaryTag(): string {
        $tagList = $this->tagList();
        return $tagList ? ucfirst(current($tagList)['name']) : '';
    }

    public function platforms(): array {
        static $platformMan;
        if (!isset($platformMan)) {
            $platformMan = new Manager\ReleasePlatform();
        }
        return $platformMan->findByRelease($this);
    }

    public function hasStreamingLinks(): bool {
        return !empty($this->platforms());
    }

    // Interface implementations
    public function categoryId(): int {
        return 1; // Music category for releases
    }

    public function categoryName(): string {
        return 'Music';
    }

    public function title(): string {
        return $this->name();
    }

    public function label(): string {
        return $this->id . " (" . $this->name() . ")";
    }

    public function canEdit(User $user): bool {
        return $user->permitted('admin_manage_releases') || $user->permitted('site_admin');
    }

    public function touch(): static {
        self::$db->prepared_query('
            UPDATE release SET
                updated = now()
            WHERE ID = ?
            ', $this->id
        );
        return $this;
    }

    public function addPlatform(string $platform, string $url, User $user): bool {
        $platformMan = new Manager\ReleasePlatform();
        return $platformMan->create($this, $platform, $url, $user) !== null;
    }

    public function removePlatform(int $platformId, User $user): bool {
        $platformMan = new Manager\ReleasePlatform();
        $platform = $platformMan->findById($platformId);
        if ($platform && $platform->releaseId() === $this->id) {
            return $platformMan->remove($platform, $user);
        }
        return false;
    }
}