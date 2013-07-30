<?php

/**
 * Helper library class.
 *
 * @author Daniel G Wood <https://github.com/danielgwood>
 */
class Helper
{
    /**
     * Escape a string for display in the browser.
     *
     * @param string $str Unsanitised string.
     * @return string
     */
    public static function escape($str)
    {
        return htmlentities((string)$str, \ENT_QUOTES, 'UTF-8');
    }

    /**
     * Format a number of minutes as a number of days.
     *
     * @param  int $minutes
     * @return string
     */
    public static function minsToDays($minutes)
    {
        return number_format((($minutes / 60) / 24), 1);
    }
}