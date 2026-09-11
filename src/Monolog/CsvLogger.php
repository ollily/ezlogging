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

use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\PlainFormatter;
use Monolog\Handler\CsvHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\PlainProcessor;
use Monolog\Processor\ProcessorInterface;

/**
 * Class CsvLogger.
 *
 * @see FileLogger
 * @see CsvHandler
 * @see PlainProcessor
 * @see PlainFormatter
 *
 * @phpstan-import-type LoggingLevel from \Monolog\AbstractEasyGoingLogger
 */
class CsvLogger extends FileLogger
{
    /** The separator char for each column */
    private string $itemSeparator;

    /** The char enclosing each column value */
    private string $itemEnclosure;

    /** @var array<string> The column header */
    private array $header;

    /**
     * @param string                                         $name          The logging channel, a simple descriptive name that is attached to all log records
     * @param array<string>                                  $header        The column header (Default: empty)
     * @param string                                         $itemSeparator The separator char for each column (Default: {@link CsvHandler::STANDARD_ITEM_SEP})
     * @param string                                         $itemEnclosure The char enclosing each column value (Default:{@link CsvHandler::STANDARD_TEXT_SEP})
     * @param null|string                                    $pathToFile    The full path to the output folder
     * @param int|\Monolog\Level|\Psr\Log\LogLevel::*|string $level         The minimum logging level at which this handler will be triggered
     *                                                                       (Default: {@link self::LEVEL_DEFAULT})
         *
     * @phpstan-param LoggingLevel $level
     *
     * @see CsvHandler::STANDARD_ITEM_SEP
     * @see CsvHandler::STANDARD_TEXT_SEP
     */
    public function __construct(
        string $name,
        array $header = [],
        string $itemSeparator = CsvHandler::STANDARD_ITEM_SEP,
        string $itemEnclosure = CsvHandler::STANDARD_TEXT_SEP,
        ?string $pathToFile = null,
        int|string|Level $level = self::LEVEL_DEFAULT
    ) {
        $this->itemSeparator = $itemSeparator;
        $this->itemEnclosure = $itemEnclosure;
        $this->header        = $header;

        parent::__construct($name, [], [], null, $pathToFile, $level);

        $this->writeHeader($this->header);
    }

    /**
     * @param string             $message    The text written to the file
     * @param array<mixed>|mixed ...$context The log context
     */
    public function out(string $message, ...$context): void
    {
        parent::info($message, $context);
    }

    /**
     * @param array<string> $header The column header
     */
    protected function writeHeader(array $header): void
    {
        if (!empty($header)) {
            $this->out('', $header);
        }
    }

    /**
     * @return string The separator char for each column
     */
    public function getItemSeparator(): string
    {
        return $this->itemSeparator;
    }

    /**
     * @return string The char enclosing each column value
     */
    public function getItemEnclosure(): string
    {
        return $this->itemEnclosure;
    }

    #[\Override]
    protected function getDefaultProcessor(): ProcessorInterface
    {
        return new PlainProcessor();
    }

    #[\Override]
    protected function getDefaultFormatter(): FormatterInterface
    {
        return new PlainFormatter();
    }

    #[\Override]
    protected function getFileHandler(?string $pathToFile, string $fileName, int|string|Level $level = self::LEVEL_DEFAULT): StreamHandler
    {
        $handler = new CsvHandler($pathToFile, $fileName, $this->itemSeparator, $this->itemEnclosure, $level);
        $handler->setFormatter($this->getDefaultFormatter());

        return $handler;
    }
}
