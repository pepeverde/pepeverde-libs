<?php

namespace Pepeverde;

class Text
{
    /**
     * @param string   $string
     * @param int      $start
     * @param int|null $length
     * @return string
     */
    public static function substr($string, $start, $length = null): string
    {
        return mb_substr($string, $start, $length, 'UTF-8');
    }

    /**
     * @param string       $haystack
     * @param array|string $needles
     * @return bool
     */
    public static function startsWith($haystack, $needles): bool
    {
        foreach ((array)$needles as $needle) {
            if ($needle !== '' && static::substr($haystack, 0, strlen($needle)) === (string)$needle) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string       $haystack
     * @param array|string $needles
     * @return bool
     */
    public static function endsWith($haystack, $needles): bool
    {
        foreach ((array)$needles as $needle) {
            if (static::substr($haystack, -strlen($needle)) === (string)$needle) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string       $haystack
     * @param array|string $needles
     * @return bool
     */
    public static function contains($haystack, $needles): bool
    {
        foreach ((array)$needles as $needle) {
            if ($needle !== '' && mb_strpos($haystack, $needle) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $string
     * @return null|string|string[]
     */
    public function br2nl($string)
    {
        return preg_replace('#<br\s*?/?>#i', "\n", $string);
    }

    /**
     * @param string $str
     * @param int    $width
     * @param string $break
     * @param bool   $cut
     * @param string $encoding
     * @return string
     */
    public function wordwrap($str, $width = 80, $break = "\n", $cut = false, $encoding = 'UTF-8'): string
    {
        $strlen = mb_strlen($str, $encoding);
        $breaklen = mb_strlen($break, $encoding);
        $newtext = '';
        if ($strlen === 0) {
            return '';
        }

        if ($breaklen === 0) {
            return false;
        }
        if ($width === 0 && $cut) {
            return false;
        }
        $laststart = $lastspace = 0;
        $breakstart = mb_substr($break, 0, 1, $encoding);
        for ($current = 0; $current < $strlen; $current++) {
            $char = mb_substr($str, $current, 1, $encoding);
            // Existing line break, copy line and  start a new one
            if ($char === $breakstart
                && $current + $breaklen < $strlen
                && mb_substr($str, $current, $breaklen, $encoding) === $break
            ) {
                $newtext .= mb_substr($str, $laststart, $current - $laststart + $breaklen, $encoding);
                $current += $breaklen - 1;
                $laststart = $lastspace = $current + 1;
            } // Keep track of spaces, if line break is necessary, do it
            else if ($char === ' ') {
                if ($current - $laststart >= $width) {
                    $newtext .= mb_substr($str, $laststart, $current - $laststart, $encoding)
                        . $break;
                    $laststart = $current + 1;
                }
                $lastspace = $current;
            } // Special cut case, if no space has been seen
            else if ($current - $laststart >= $width
                && $cut && $laststart >= $lastspace
            ) {
                $newtext .= mb_substr($str, $laststart, $current - $laststart, $encoding)
                    . $break;
                $laststart = $lastspace = $current;
            } // Usual case that line got longer than expected
            else if ($current - $laststart >= $width
                && $laststart < $lastspace
            ) {
                $newtext .= mb_substr($str, $laststart, $lastspace - $laststart, $encoding)
                    . $break;
                // $laststart = $lastspace = $lastspace + 1;
                $laststart = ++$lastspace;
            }
        }
        // Rest of the string
        if ($laststart !== $current) {
            $newtext .= mb_substr($str, $laststart, $current - $laststart, $encoding);
        }
        return $newtext;
    }

    /**
     * @param string $string
     * @param int    $length
     * @param string $separator
     * @param bool   $preserve
     * @return string
     */
    public function truncate($string, $length = 30, $separator = '...', $preserve = false): string
    {
        if (mb_strlen($string, 'UTF-8') > $length) {
            if (true === $preserve && false !== ($breakpoint = mb_strpos($string, ' ', $length, 'UTF-8'))) {
                $length = $breakpoint;
            }

            return rtrim(static::substr($string, 0, $length)) . $separator;
        }

        return $string;
    }

    /**
     * @param string $string
     * @param int    $length
     * @param string $separator
     * @return string
     */
    public function truncateHtml($string, $length = 300, $separator = '&hellip;'): string
    {
        // TODO use mbstring functions
        $i = 0;
        $tags = [];
        $m = [];

        preg_match_all('/<[^>]+>([^<]*)/', $string, $m, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
        foreach ($m as $o) {
            if ($o[0][1] - $i >= $length) {
                break;
            }
            $t = static::substr(strtok($o[0][0], " \t\n\r\0\x0B>"), 1);
            if ($t[0] !== '/') {
                $tags[] = $t;
            } elseif (end($tags) === static::substr($t, 1)) {
                array_pop($tags);
            }
            $i += $o[1][1] - $o[0][1];
        }

        return static::substr($string, 0, $length = min(strlen($string), $length + $i)) . (count(
                $tags = array_reverse($tags)
            ) ? '</' . implode(
                    '></',
                    $tags
                ) . '>' : '') . (mb_strlen($string) > $length ? $separator : '');
    }
}
