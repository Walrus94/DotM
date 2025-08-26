<?php

namespace Gazelle;

class ReleaseLink extends BaseObject {
    final public const tableName = 'release_platform';

    protected ?TGroup $tgroup = null;

    public function flush(): static {
        unset($this->info);
        unset($this->tgroup);
        return $this;
    }

    public function link(): string {
        return sprintf('<a href="%s">%s</a>', $this->url(), display_str($this->platform()));
    }

    public function location(): string {
        return $this->url();
    }

    protected function infoRow(): array {
        return $this->info ??= self::$db->rowAssoc(
            "SELECT ReleaseID AS release_id,
                Platform AS platform,
                Url      AS url,
                Format   AS format,
                Bitrate  AS bitrate
            FROM release_platform
            WHERE ID = ?",
            $this->id
        );
    }

    public function tgroupId(): int {
        return (int)$this->infoRow()['release_id'];
    }

    public function tgroup(): TGroup {
        return $this->tgroup ??= new TGroup($this->tgroupId());
    }

    public function platform(): string {
        return $this->infoRow()['platform'];
    }

    public function url(): string {
        return $this->infoRow()['url'];
    }

    public function format(): ?string {
        return $this->infoRow()['format'];
    }

    public function bitrate(): ?string {
        return $this->infoRow()['bitrate'];
    }

    public function info(): array {
        $row = $this->infoRow();
        return [
            'id'       => $this->id,
            'platform' => $row['platform'],
            'url'      => $row['url'],
            'format'   => $row['format'],
            'bitrate'  => $row['bitrate'],
        ];
    }
}
