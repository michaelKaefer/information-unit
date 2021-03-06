<?php

declare(strict_types=1);

namespace Unit\Information;

final class PhpShorthandValueToBytesConverter
{
    /**
     * PHP allows to use shorthand values like "128M" in some places. These values do
     * not follow the ICE standard. See
     * https://www.php.net/manual/en/faq.using.php#faq.using.shorthandbytes.
     *
     * This method is taken from
     * https://github.com/symfony/symfony/blob/master/src/Symfony/Component/HttpKernel/DataCollector/MemoryDataCollector.php.
     *
     * @return int|float The configured memory limit can exceed the range that can be
     *                   represented by an integer.
     */
    public static function convert(string $phpShorthandValue)
    {
        if ('-1' === $phpShorthandValue) {
            throw new InvalidPhpShorthandValueException();
        }

        $phpShorthandValue = strtolower($phpShorthandValue);
        $max = strtolower(ltrim($phpShorthandValue, '+'));
        if (0 === strpos($max, '0x')) {
            $max = \intval($max, 16);
        } elseif (0 === strpos($max, '0')) {
            $max = \intval($max, 8);
        } else {
            $max = (int) $max;
        }

        switch (substr($phpShorthandValue, -1)) {
            case 't': $max *= 1024;
            // no break
            case 'g': $max *= 1024;
            // no break
            case 'm': $max *= 1024;
            // no break
            case 'k': $max *= 1024;
        }

        return $max;
    }
}
