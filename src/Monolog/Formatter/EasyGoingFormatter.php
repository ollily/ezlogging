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

namespace Monolog\Formatter;

/**
 * Class EasyGoingFormatter.
 *
 * @see LineFormatter
 */
class EasyGoingFormatter extends LineFormatter
{
    /** Default output format */
    public const string FORMATTER_OUTPUT = "\n%datetime% [%extra.level_name_pad%] %channel%->%extra.xFunction%() - %message% %context%";

    /** Default datetime format */
    public const string FORMATTER_DATEFORMAT = "Ymd-Gis.v";

    /**
     * EasyGoingFormatter constructor.
     *
     * @param null|string $format                     Is ignored (Default: {@link EasyGoingFormatter::FORMATTER_OUTPUT})
     * @param null|string $dateFormat                 Is ignored (Default: {@link EasyGoingFormatter::FORMATTER_DATEFORMAT})
     * @param bool        $allowInlineLineBreaks      Whether to allow inline line breaks in log entries (Default: true)
     * @param bool        $ignoreEmptyContextAndExtra Whether ignore context and extra data in output (Default: true)
     * @param bool        $includeStacktraces         Add stacktrace to output (Default: false)
     *
     * @see EasyGoingFormatter::FORMATTER_OUTPUT
     * @see EasyGoingFormatter::FORMATTER_DATEFORMAT
     */
    public function __construct(
        ?string $format = self::FORMATTER_OUTPUT,
        ?string $dateFormat = self::FORMATTER_DATEFORMAT,
        bool $allowInlineLineBreaks = true,
        bool $ignoreEmptyContextAndExtra = true,
        bool $includeStacktraces = false
    ) {
        parent::__construct(
            self::FORMATTER_OUTPUT,
            self::FORMATTER_DATEFORMAT,
            true,
            true,
            false
        );
    }
}
