<?php

namespace Gazelle\Json;

class ReleaseLink extends \Gazelle\Json {
    public function __construct(
        protected \Gazelle\ReleaseLink $link,
        protected \Gazelle\User        $user,
        protected \Gazelle\Manager\ReleaseLink $linkMan,
    ) {}

    public function linkPayload(): array {
        return [
            'id'       => $this->link->id(),
            'platform' => $this->link->platform(),
            'url'      => $this->link->url(),
            'format'   => $this->link->format(),
            'bitrate'  => $this->link->bitrate(),
        ];
    }

    public function payload(): array {
        return [
            'group' => (new TGroup($this->link->tgroup(), $this->user, new \Gazelle\Manager\Torrent()))->tgroupPayload(),
            'link'  => $this->linkPayload(),
        ];
    }
}
