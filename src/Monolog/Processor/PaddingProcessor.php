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

namespace Monolog\Processor;

use Monolog\Level;
use Monolog\Logger;
use Monolog\LogRecord;

/**
 * Class PaddingProcessor.
 *
 * Use Introspection from @see IntrospectionProcessor.
 *
 * @phpstan-type LoggingLevel value-of<\Monolog\Level>|\Monolog\Level|\Psr\Log\LogLevel::*
 *
 * @see     IntrospectionProcessor
 */
class PaddingProcessor implements ProcessorInterface
{
    public const string OFFSET_EXTRA = 'extra';

    public const string OFFSET_LEVEL_NAME_PAD = 'level_name_pad';

    public const string OFFSET_LEVEL_NAME = 'level_name';

    public const string OFFSET_LEVEL = 'level';

    public const string OFFSET_XDETAILS = 'xdetails';

    public const int LEVEL_WIDTH = 8;

    public const string LEVEL_CHAR = ' ';

    /** @var array<string> */
    protected const array SKIP_FUNCTIONS = [
        'call_user_func',
        'call_user_func_array',
    ];

    /** @var array<string> */
    protected const array SKIP_CLASSES = [
        'Monolog\\',
    ];

    private Level $level;

    /** @var array<string> */
    private array $skipClassesPartials;

    private int $skipStackFramesCount;

    /**
 * @param int|\Monolog\Level|\Psr\Log\LogLevel::*|string $level The minimum logging level at which this handler will be triggered
     *                                                              (Default: {@link Level::Debug})
         * @param string[]                                       $skipClassesPartials
     * @param int                                            $skipStackFramesCount
     *
     * @phpstan-param LoggingLevel $level
     */
    public function __construct(int|string|Level $level = Level::Debug, array $skipClassesPartials = [], int $skipStackFramesCount = 0)
    {
        /** @psalm-suppress PossiblyInvalidArgument  */
        $this->level = Logger::toMonologLevel($level);
        $this->skipClassesPartials = array_merge(static::SKIP_CLASSES, $skipClassesPartials);
        $this->skipStackFramesCount = $skipStackFramesCount;
    }

    /**
     * @param LogRecord $record A record
     *
     * @return LogRecord The processed record
     */
    #[\Override]
    public function __invoke(LogRecord $record): LogRecord
    {
        // return if the level is not high enough
        if ($record->level->isLowerThan($this->level)) {
            return $record;
        }

        /** @var array<mixed,mixed> */
        $extra = $record[self::OFFSET_EXTRA];

        /** @var string $levelName */
        $levelName = $record[self::OFFSET_LEVEL_NAME];
        $extra[self::OFFSET_LEVEL_NAME_PAD] = str_pad($levelName, self::LEVEL_WIDTH, self::LEVEL_CHAR, STR_PAD_RIGHT);
        $record->offsetSet(self::OFFSET_EXTRA, $extra);

        return $this->__invokeIntrospection($record);
    }

    private function __invokeIntrospection(LogRecord $record): LogRecord // NOSONAR: php:S100
    {
        // return if the level is not high enough
        if ($record->level->isLowerThan($this->level)) {
            return $record;
        }

        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

        // skip first since it's always the current method
        array_shift($trace);
        // the call_user_func call is also skipped
        array_shift($trace);

        $index = 0;

        while ($this->isTraceClassOrSkippedFunction($trace, $index)) {
            if (isset($trace[$index]['class'])) {
                foreach ($this->skipClassesPartials as $part) {
                    if (str_contains($trace[$index]['class'], $part)) {
                        $index++;

                        continue 2;
                    }
                }
            } elseif (in_array($trace[$index]['function'], static::SKIP_FUNCTIONS, true)) {
                $index++;

                continue;
            } else {
                break;
            }
            break;
        }

        $index += $this->skipStackFramesCount;

        // we should have the call source now
        if ($index > 0 && $index < count($trace)) {
            $curTrace  = $trace[$index];
            $prevTrace = $trace[$index - 1];

            $record->extra = array_merge(
                $record->extra,
                [
                    'xFile'     => $prevTrace['file'] ?? null,
                    'xLine'     => $prevTrace['line'] ?? null,
                    'xClass'    => $curTrace['class'] ?? null,
                    'xCallType' => $curTrace['type'] ?? null,
                    'xFunction' => $curTrace['function'],
                ]
            );
        }

        return $record;
    }

    /**
     * @param array<mixed,mixed> $trace
     * @param int                $index
     *
     * @return bool
     */
    private function isTraceClassOrSkippedFunction(array $trace, int $index): bool
    {
        if (!isset($trace[$index])) {
            return false;
        }

        return isset($trace[$index]['class']) || \in_array($trace[$index]['function'], static::SKIP_FUNCTIONS, true);
    }
}
