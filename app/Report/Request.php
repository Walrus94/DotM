<?php

namespace Gazelle\Report;

/**
 * Request Reports have been disabled for music catalog.
 * All torrent-related request functionality has been removed.
 */
class Request extends AbstractReport {
    protected bool $isUpdate = false;

    public function __construct(
        protected readonly int $reportId,
        protected readonly \Gazelle\Request $subject,
    ) {}

    public function template(): string {
        // Request system disabled for music catalog
        return 'report/disabled.twig';
    }

    public function bbLink(): string {
        // Request system disabled for music catalog
        return "the request [disabled]Request system disabled[/disabled]";
    }

    public function titlePrefix(): string {
        // Request system disabled for music catalog
        return 'Request System Disabled: ';
    }

    public function title(): string {
        // Request system disabled for music catalog
        return 'Request system has been disabled for music catalog';
    }

    public function isUpdate(bool $isUpdate): static {
        // Request system disabled for music catalog
        $this->isUpdate = false;
        return $this;
    }

    public function needReason(): bool {
        // Request system disabled for music catalog
        return false;
    }
}
