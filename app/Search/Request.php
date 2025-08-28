<?php

namespace Gazelle\Search;

/**
 * Request Search has been disabled for music catalog.
 * All torrent-related request functionality has been removed.
 */
class Request extends \Gazelle\Base {
    protected bool $negate;
    protected int $total;
    protected int $bookmarkerId;
    protected string $text;
    protected string $title;
    protected string $tagList;
    protected array $encodingList;
    protected array $formatList;
    protected array $mediaList;
    protected array $releaseTypeList;
    protected array $list;
    protected \SphinxqlQuery $sphinxq;

    public function __construct(
        protected \Gazelle\Manager\Request $manager,
    ) {
        $this->sphinxq = new \SphinxqlQuery();
    }

    public function isBookmarkView(): bool {
        // Request system disabled for music catalog
        return false;
    }

    public function setBookmarker(\Gazelle\User $user): static {
        // Request system disabled for music catalog
        $this->text = "Request system disabled";
        $this->title = "Request system disabled";
        $this->bookmarkerId = 0;
        return $this;
    }

    public function setCategory(array $categoryList): static {
        // Request system disabled for music catalog
        return $this;
    }

    public function setCreator(\Gazelle\User $user): static {
        // Request system disabled for music catalog
        $this->text = "Request system disabled";
        $this->title = "Request system disabled";
        return $this;
    }

    public function setFiller(\Gazelle\User $user): static {
        // Request system disabled for music catalog
        $this->text = "Request system disabled";
        $this->title = "Request system disabled";
        return $this;
    }

    public function setEncoding(array $encodingList, bool $strict): static {
        // Request system disabled for music catalog
        $this->encodingList = [];
        $this->negate = false;
        return $this;
    }

    public function setFormat(array $formatList, bool $strict): static {
        // Request system disabled for music catalog
        $this->formatList = [];
        $this->negate = false;
        return $this;
    }

    public function setMedia(array $mediaList, bool $strict): static {
        // Request system disabled for music catalog
        $this->mediaList = [];
        $this->negate = false;
        return $this;
    }

    public function setReleaseType(array $releaseTypeList, bool $strict): static {
        // Request system disabled for music catalog
        $this->releaseTypeList = [];
        return $this;
    }

    public function setTags(array $tagList, bool $strict): static {
        // Request system disabled for music catalog
        $this->tagList = '';
        return $this;
    }

    public function setText(string $text): static {
        // Request system disabled for music catalog
        $this->text = "Request system disabled";
        return $this;
    }

    public function setYear(int $year): static {
        // Request system disabled for music catalog
        return $this;
    }

    public function setLimit(int $limit, int $offset): static {
        // Request system disabled for music catalog
        return $this;
    }

    public function execute(string $orderBy, string $direction): int {
        // Request system disabled for music catalog
        $this->total = 0;
        $this->list = [];
        return 0;
    }

    public function list(): array {
        // Request system disabled for music catalog
        return [];
    }

    public function total(): int {
        // Request system disabled for music catalog
        return 0;
    }

    public function encodingList(): array {
        // Request system disabled for music catalog
        return [];
    }

    public function formatList(): array {
        // Request system disabled for music catalog
        return [];
    }

    public function mediaList(): array {
        // Request system disabled for music catalog
        return [];
    }

    public function releaseTypeList(): array {
        // Request system disabled for music catalog
        return [];
    }

    public function tagList(): string {
        // Request system disabled for music catalog
        return '';
    }

    public function text(): string {
        // Request system disabled for music catalog
        return 'Request system disabled';
    }

    public function title(): string {
        // Request system disabled for music catalog
        return 'Request system disabled';
    }
}
