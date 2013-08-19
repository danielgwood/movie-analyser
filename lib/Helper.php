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
     * Strip accented characters from a string.
     *
     * Note: In PHP6 unicode support will apparently be better, so there may be
     * more effective ways to do this. Right now though, the least messy solution
     * seems to be writing out all the replacements (or copypasting from SO as I have)
     *
     * @param  string $str
     * @return string
     * @see http://stackoverflow.com/questions/3371697/replacing-accented-characters-php
     */
    public static function normaliseChars($str)
    {
        $replace = array(
            'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'Ae', 'Å' => 'A', 'Æ' => 'A', 'Ă' => 'A',
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'ae', 'å' => 'a', 'ă' => 'a', 'æ' => 'ae',
            'þ' => 'b', 'Þ' => 'B',
            'Ç' => 'C', 'ç' => 'c',
            'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
            'Ğ' => 'G', 'ğ' => 'g',
            'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'İ' => 'I', 'ı' => 'i', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
            'Ñ' => 'N',
            'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'Oe', 'Ø' => 'O', 'ö' => 'oe', 'ø' => 'o',
            'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o',
            'Š' => 'S', 'š' => 's', 'Ş' => 'S', 'ș' => 's', 'Ș' => 'S', 'ş' => 's', 'ß' => 'ss',
            'ț' => 't', 'Ț' => 'T',
            'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'Ue',
            'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'ue',
            'Ý' => 'Y',
            'ý' => 'y', 'ý' => 'y', 'ÿ' => 'y',
            'Ž' => 'Z', 'ž' => 'z'
        );

        return strtr($str, $replace);
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