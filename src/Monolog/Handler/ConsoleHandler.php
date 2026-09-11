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
 * Class ConsoleHandler.
 *
 * @see StreamHandler
 *
 * @phpstan-type LoggingLevel value-of<\Monolog\Level>|\Monolog\Level|\Psr\Log\LogLevel::*
 */
class ConsoleHandler extends StreamHandler
{
    /** Standard output stream */
    public const string HANDLER_STDOUT = "php://stdout";

    /** Default output level */
    public const Level LEVEL_DEFAULT =  Level::Info;

    /**
     * ConsoleHandler constructor.
     *
 * @param int|\Monolog\Level|\Psr\Log\LogLevel::*|string $level The minimum logging level at which this handler will be triggered
     *                                                              (Default: {@link self::LEVEL_DEFAULT})
         *
     * @phpstan-param LoggingLevel $level
     *
     * @see ConsoleHandler::LEVEL_DEFAULT;
     */
    public function __construct(int|string|Level $level = self::LEVEL_DEFAULT)
    {
        parent::__construct(self::HANDLER_STDOUT, $level);
    }
}
