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

namespace Monolog\Handler;

use Monolog\Level;
use Monolog\LogRecord;
use ollily\Tools\PhpVersionTrait;
use ollily\Tools\String\ImplodeTrait;

/**
 * Stores any data (given as array) to a csv file.
 *
 * Inspired by {@see https://github.com/femtopixel/monolog-csvhandler}.
 * Original by @author Jay MOULIN <jay@femtopixel.com>
 *
 * @see FileHandler
 *
 * @phpstan-import-type LoggingLevel from \Monolog\Handler\ConsoleHandler
 */
class CsvHandler extends FileHandler
{
    use PhpVersionTrait;
    use ImplodeTrait;

    /** Fallback filename */
    public const string STANDARD_FILENAME = 'CsvFile';

    /** Default file extension */
    public const string STANDARD_FILEEXT     = '.csv';

    /** Default separator char for each column */
    public const string STANDARD_ITEM_SEP    = ';';

    /** Default char enclosing each column value */
    public const string STANDARD_TEXT_SEP    = '"';

    /** Default char for escaping char */
    public const string STANDARD_ESCAPE_CHAR = '\\';

    /** Relevant PHP version */
    protected const string CHECKVERSION = '5.5.4';

    /** Key of the formatted in the output record */
    protected const string KEY_FORMATTED        = 'formatted';

    /** The separator char for each column */
    private string $itemSeparator;

    /** The char enclosing each column value */
    private string $itemEnclosure;

    /**
     * CsvHandler constructor.
     *
     * @param null|string                                    $pathToFile    The full path to the output folder
     * @param null|string                                    $fileName      The name of the output file
     * @param string                                         $itemSeparator The separator char for each column (Default: {@link CsvHandler::STANDARD_ITEM_SEP})
     * @param string                                         $itemEnclosure The char enclosing each column value (Default:{@link CsvHandler::STANDARD_TEXT_SEP})
 * @param int|\Monolog\Level|\Psr\Log\LogLevel::*|string $level The minimum logging level at which this handler will be triggered
     *                                                              (Default: {@link self::LEVEL_DEFAULT})
         *
     * @phpstan-param LoggingLevel $level
     *
     * @see CsvHandler::STANDARD_ITEM_SEP
     * @see CsvHandler::STANDARD_TEXT_SEP
     * @see FileHandler::LEVEL_DEFAULT
     */
    public function __construct(
        ?string $pathToFile = null,
        ?string $fileName = null,
        string $itemSeparator = self::STANDARD_ITEM_SEP,
        string $itemEnclosure = self::STANDARD_TEXT_SEP,
        int|string|Level $level = self::LEVEL_DEFAULT
    ) {
        parent::__construct($pathToFile, $fileName, $level);
        $this->itemSeparator = $itemSeparator;
        $this->itemEnclosure = $itemEnclosure;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    protected function streamWrite($stream, LogRecord $record): void
    {
        $output = [];

        /** @psalm-suppress RiskyTruthyFalsyComparison */
        if (isset($record[self::KEY_MESSAGE]) && !empty($record[self::KEY_MESSAGE])) {
            array_push($output, $record[self::KEY_MESSAGE]);
        }

        /** @psalm-suppress RiskyTruthyFalsyComparison */
        if (isset($record[self::KEY_CONTEXT]) && !empty($record[self::KEY_CONTEXT])) {
            $implodeContext = $this->array_flatten($record[self::KEY_CONTEXT]); // @phpstan-ignore argument.type
            $output = array_merge($output, $implodeContext);
        }
        if ($this->isPhpGreater(self::CHECKVERSION)) {
            fputcsv($stream, $output, $this->itemSeparator, $this->itemEnclosure, static::STANDARD_ESCAPE_CHAR);
        } else {
            fputcsv($stream, $output, $this->itemSeparator, $this->itemEnclosure);
        }
    }
}
