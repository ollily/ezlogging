<?php

declare(strict_types=1);

/*
 * This file is part of ezlogging
 *
 * (c) 2025 Oliver Glowa, coding.glowa.com
 *
 * This source file is subject to the Apache-2.0 license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Monolog;

use Closure;
use DateTimeZone;
use Monolog\Handler\HandlerInterface;
use Monolog\Processor\ProcessorInterface;
use Psr\Log\AbstractLogger;

/**
 * @phpstan-import-type LoggingLevel from AbstractEasyGoingLogger
 */
class LoggerAdapter extends AbstractLogger implements ResettableInterface  // NOSONAR: php:S1448 
{
    /** The real monolog logger */
    private Logger $monologLogger;

    /**
     * @param string                 $name       The logging channel, a simple descriptive name that is attached to all log records
     * @param list<HandlerInterface> $handlers   optional stack of handlers, the first one in the array is called first, etc
     * @param callable[]             $processors Optional array of processors
     * @param null|DateTimeZone      $timezone   Optional timezone, if not provided date_default_timezone_get() will be used
     */
    public function __construct(string $name, array $handlers = [], array $processors = [], ?DateTimeZone $timezone = null)
    {
        $this->monologLogger = new Logger($name, $handlers, $processors, $timezone);
    }

    // LoggerInterface

    #[\Override]
    public function alert(string|\Stringable $message, mixed $context = []): void
    {
        $this->monologLogger->alert($message, $context);
    }

    #[\Override]
    public function critical(string|\Stringable $message, mixed $context = []): void
    {
        $this->monologLogger->critical($message, $context);
    }

    #[\Override]
    public function debug(string|\Stringable $message, mixed $context = []): void
    {
        $this->monologLogger->debug($message, $context);
    }

    #[\Override]
    public function emergency(string|\Stringable $message, mixed $context = []): void
    {
        $this->monologLogger->emergency($message, $context);
    }

    #[\Override]
    public function error(string|\Stringable $message, mixed $context = []): void
    {
        $this->monologLogger->error($message, $context);
    }

    #[\Override]
    public function info(string|\Stringable $message, mixed $context = []): void
    {
        $this->monologLogger->info($message, $context);
    }

    #[\Override]
    public function log($level, string|\Stringable $message, mixed $context = []): void
    {
        $this->monologLogger->log($level, $message, $context);
    }

    #[\Override]
    public function notice(string|\Stringable $message, mixed $context = []): void
    {
        $this->monologLogger->notice($message, $context);
    }

    #[\Override]
    public function warning(string|\Stringable $message, mixed $context = []): void
    {
        $this->monologLogger->warning($message, $context);
    }

    // ResettableInterface

    #[\Override]
    public function reset(): void
    {
        $this->monologLogger->reset();
    }

    // Logger methods

    /**
     * @param int                                    $level    The logging level (a Monolog or RFC 5424 level)
     * @param string                                 $message  The log message
     * @param mixed[]                                $context  The log context
     * @param null|JsonSerializableDateTimeImmutable $datetime Optional log date to log into the past or future
     *
     * @psalm-suppress UnresolvableConstant
     *
     * @phpstan-param value-of<Level::VALUES>|Level $level
     *
     * @return bool Whether the record has been processed
     */
    public function addRecord(int|Level $level, string $message, array $context = [], JsonSerializableDateTimeImmutable|null $datetime = null): bool
    {
        return $this->monologLogger->addRecord($level, $message, $context, $datetime);
    }

    public function close(): void
    {
        $this->monologLogger->close();
    }

    public function getExceptionHandler(): Closure|null
    {
        return $this->monologLogger->getExceptionHandler();
    }

    /**
     * @return list<HandlerInterface>
     */
    public function getHandlers(): array
    {
        return $this->monologLogger->getHandlers();
    }

    public function getName(): string
    {
        return $this->monologLogger->getName();
    }

    /**
     * @return callable[]
     *
     * @phpstan-return array<ProcessorInterface|(callable(LogRecord): LogRecord)>
     */
    public function getProcessors(): array
    {
        return $this->monologLogger->getProcessors();
    }

    public function getTimezone(): \DateTimeZone
    {
        return $this->monologLogger->getTimezone();
    }

    /**
     * @param int|Level|string $level
     *
     * @psalm-suppress UnresolvableConstant
     *
     * @phpstan-param value-of<Level::VALUES>|value-of<Level::NAMES>|Level|\Psr\Log\LogLevel::* $level
     */
    public function isHandling(int|string|Level $level): bool
    {
        return $this->monologLogger->isHandling($level);
    }

    /**
     * @throws \LogicException If empty handler stack
     */
    public function popHandler(): HandlerInterface
    {
        return $this->monologLogger->popHandler();
    }

    /**
     * @phpstan-return ProcessorInterface|(callable(LogRecord): LogRecord)
     *
     * @throws \LogicException If empty processor stack
     */
    public function popProcessor(): callable
    {
        return $this->monologLogger->popProcessor();
    }

    /**
     * @param HandlerInterface $handler
     *
     * @return $this
     */
    public function pushHandler(HandlerInterface $handler): self
    {
        $this->monologLogger->pushHandler($handler);

        return $this;
    }

    /**
     * @param callable|ProcessorInterface $callback
     *
     * @phpstan-param ProcessorInterface|(callable(LogRecord): LogRecord) $callback
     *
     * @return $this
     */
    public function pushProcessor(ProcessorInterface|callable $callback): self
    {
        $this->monologLogger->pushProcessor($callback);

        return $this;
    }

    /**
     * @param null|Closure $callback
     *
     * @return $this
     */
    public function setExceptionHandler(Closure|null $callback): self
    {
        $this->monologLogger->setExceptionHandler($callback);

        return $this;
    }

    /**
     * @param list<HandlerInterface> $handlers
     *
     * @return $this
     */
    public function setHandlers(array $handlers): self
    {
        $this->monologLogger->setHandlers($handlers);

        return $this;
    }

    /**
     * @param DateTimeZone $timeZone
     *
     * @return $this
     */
    public function setTimezone(DateTimeZone $timeZone): self
    {
        $this->monologLogger->setTimezone($timeZone);

        return $this;
    }

    /**
     * @param bool $detectCycles
     *
     * @return $this
     */
    public function useLoggingLoopDetection(bool $detectCycles): self
    {
        $this->monologLogger->useLoggingLoopDetection($detectCycles);

        return $this;
    }

    /**
     * @param bool $micro True to use microtime() to create timestamps
     *
     * @return $this
     */
    public function useMicrosecondTimestamps(bool $micro): self
    {
        $this->monologLogger->useLoggingLoopDetection($micro);

        return $this;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function withName(string $name): self
    {
        $this->monologLogger->withName($name);

        return $this;
    }
}
