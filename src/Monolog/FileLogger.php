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

use DateTimeZone;
use Monolog\Handler\FileHandler;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\StreamHandler;

/**
 * Class FileLogger.
 *
 * @see ConsoleLogger
 * @see FileHandler
 *
 * @phpstan-import-type LoggingLevel from \Monolog\AbstractEasyGoingLogger
 * @phpstan-import-type ProcessorCallable from \Monolog\AbstractEasyGoingLogger
 */
class FileLogger extends ConsoleLogger
{
    private HandlerInterface $fileHandler;

    /**
     * @param string                                         $name       The logging channel, a simple descriptive name that is attached to all log records
     * @param list<handlerInterface>                         $handlers   Optional stack of handlers, the first one in the array is called first, etc
     * @param callable[]                                     $processors Optional array of processors
     * @param null|DateTimeZone                              $timezone   Optional timezone, if not provided date_default_timezone_get() will be used
     * @param null|string                                    $pathToFile The full path to the output folder
     * @param int|\Monolog\Level|\Psr\Log\LogLevel::*|string $level      The minimum logging level at which this handler will be triggered
     *                                                                   (Default: {@link self::LEVEL_DEFAULT})
     *
     * @phpstan-param LoggingLevel      $level
     * @phpstan-param ProcessorCallable $processors
     *
     * @see AbstractEasyGoingLogger::LEVEL_DEFAULT
     */
    public function __construct(
        string $name,
        array $handlers = [],
        array $processors = [],
        ?DateTimeZone $timezone = null,
        ?string $pathToFile = null,
        int|string|Level $level = self::LEVEL_DEFAULT
    ) {
        $this->fileHandler = $this->getFileHandler($pathToFile, $name);

        parent::__construct($name, $handlers, $processors, $timezone, $level);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function getDefaultHandler(int|string|Level $level = self::LEVEL_DEFAULT): HandlerInterface
    {
        return $this->fileHandler;
    }

    /**
     * @param null|string                                    $pathToFile The full path to the output folder
     * @param string                                         $fileName   The name of the output file
     * @param int|\Monolog\Level|\Psr\Log\LogLevel::*|string $level      The minimum logging level at which this handler will be triggered
     *                                                                   (Default: {@link self::LEVEL_DEFAULT})
     *
     * @phpstan-param LoggingLevel $level
     *
     * @return StreamHandler the stream handler for the file
     */
    protected function getFileHandler(?string $pathToFile, string $fileName, int|string|Level $level = self::LEVEL_DEFAULT): StreamHandler
    {
        return new FileHandler($pathToFile, $fileName, $level);
    }

    /**
     * @return string The full filename (with path) of the output file or empty string
     */
    public function getFileName(): string
    {
        $fileName = '';
        foreach ($this->getHandlers() as $handler) {
            if ($handler instanceof FileHandler) {
                $fileName = $handler->getFileName();
            }
        }

        return $fileName;
    }
}
