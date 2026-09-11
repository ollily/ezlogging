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

/**
 * Class FileHandler.
 *
 * @see StreamHandler
 *
 * @phpstan-import-type LoggingLevel from \Monolog\Handler\ConsoleHandler
 */
class FileHandler extends StreamHandler
{
    /** Fallback filename */
    public const string STANDARD_FILENAME = 'File';

    /** Default file extension */
    public const string STANDARD_FILEEXT  = '.log';

    /** @var int|Level|string Default output level (DEBUG) */
    public const mixed LEVEL_DEFAULT =  Level::Debug;

    /** Default separator char for namespace (Default) */
    public const string C_NS_SEP  = "\\";

    /** Default separator char for namespace (Filesystem) */
    public const string C_NS_FS_SEP = '_';

    /** Key of the message in the output record */
    protected const string KEY_MESSAGE = 'message';

    /** Key of the contect in the output record */
    protected const string KEY_CONTEXT = 'context';

    /** Temporary folder */
    private static string $tmpDir;

    /** The full filename (with path) of the output file */
    private string $fileName;

    /**
     * @param null|string $pathToFile The full path to the output folder
     * @param null|string $fileName   The name of the output file
     *
     * @return string The full filename (with path) of the output file
     */
    public static function prepareFileName(?string $pathToFile = null, ?string $fileName = ''): string
    {
        if (is_null($pathToFile) || empty($pathToFile)) {
            $pathToFile = self::$tmpDir;
        }
        if (is_null($fileName) || empty($fileName)) {
            $fileName = static::STANDARD_FILENAME;
        }

        return $pathToFile . DIRECTORY_SEPARATOR . str_replace(self::C_NS_SEP, self::C_NS_FS_SEP, $fileName . static::STANDARD_FILEEXT);
    }

    /**
     * FileHandler constructor.
     *
     * @param null|string                                    $pathToFile The full path to the output folder
     * @param null|string                                    $fileName   The name of the output file
 * @param int|\Monolog\Level|\Psr\Log\LogLevel::*|string $level The minimum logging level at which this handler will be triggered
     *                                                              (Default: {@link self::LEVEL_DEFAULT})
         *
     * @phpstan-param LoggingLevel $level
     *
     * @see FileHandler::LEVEL_DEFAULT
     */
    public function __construct(
        ?string $pathToFile = null,
        ?string $fileName = null,
        int|string|Level $level = self::LEVEL_DEFAULT
    ) {
        self::$tmpDir   = sys_get_temp_dir();
        $this->fileName = static::prepareFileName($pathToFile, $fileName);
        parent::__construct($this->fileName, $level);
    }

    /**
     * @return string The full filename (with path) of the output file
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }
}
