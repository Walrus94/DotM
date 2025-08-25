<?php

namespace Gazelle;


abstract class Task extends Base {
    protected array $events = [];
    protected int $processed = 0;
    protected float $startTime;
    protected int $historyId;

    public function __construct(
        protected readonly int $taskId,
        protected readonly string $name,
        protected readonly bool $isDebug,
    ) {}

    public function begin(): void {
        $this->startTime = microtime(true);
        self::$db->prepared_query('
            INSERT INTO periodic_task_history
                   (periodic_task_id)
            VALUES (?)
        ', $this->taskId);

        $this->historyId = self::$db->inserted_id();
    }

    public function end(bool $sane): int {
        $elapsed = (microtime(true) - $this->startTime) * 1000;
        $errorCount = count(array_filter($this->events, fn($event) => $event->severity === 'error'));
        self::$db->prepared_query('
            UPDATE periodic_task_history SET
                status = ?,
                num_errors = ?,
                num_items = ?,
                duration_ms = ?
            WHERE periodic_task_history_id = ?
            ', 'completed', $errorCount, $this->processed, $elapsed, $this->historyId
        );

        echo("DONE! (" . number_format(microtime(true) - $this->startTime, 3) . ")\n");

        foreach ($this->events as $event) {
            printf("%s [%s] (%d) %s\n", $event->timestamp, $event->severity, $event->reference, $event->event);
            self::$db->prepared_query('
                INSERT INTO periodic_task_history_event
                       (periodic_task_history_id, severity, event_time, event,             reference)
                VALUES (?,                        ?,        ?,          substr(?, 1, 255), ?)
            ', $this->historyId, $event->severity, $event->timestamp, $event->event, $event->reference);
        }

        if ($errorCount > 0 && $sane) {
            self::$db->prepared_query('
                UPDATE periodic_task SET
                    is_sane = FALSE
                WHERE periodic_task_id = ?
            ', $this->taskId);
            self::$cache->delete_value(TaskScheduler::CACHE_TASKS);

            // IRC notifications removed
        } elseif ($errorCount == 0 && !$sane) {
            self::$db->prepared_query('
                UPDATE periodic_task SET
                    is_sane = TRUE
                WHERE periodic_task_id = ?
            ', $this->taskId);
            self::$cache->delete_value(TaskScheduler::CACHE_TASKS);

            // IRC notifications removed
        }
        return $this->processed;
    }

    public function log(string $message, string $severity = 'info', int $reference = 0): void {
        if (!$this->isDebug && $severity === 'debug') {
            return;
        }
        $this->events[] = new TaskScheduler\Event($severity, $message, $reference, \Gazelle\Util\Time::sqlTime());
    }

    public function debug(string $message, int $reference = 0): void {
        $this->log($message, 'debug', $reference);
    }

    public function info(string $message, int $reference = 0): void {
        $this->log($message, 'info', $reference);
    }

    public function error(string $message, int $reference = 0): void {
        $this->log($message, 'error', $reference);
    }

    abstract public function run(): void;
}
