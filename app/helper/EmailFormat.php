<?php
namespace Helper;

class EmailFormat {
    /**
     * Парсинг email для отображения
     *
     * @param $string
     * @param bool $hide
     * @return string
     */
    static public function exec($string, $hide = false) {
        if(!filter_var($string, FILTER_VALIDATE_EMAIL)) {
            return '';
        }

        if(!$hide) {
            return '';
        }

        preg_match('@^(.*)?\@(.*)?\.([a-zа-я0-9]+)$@ui', $string, $match);

        return self::_strpad($match[1]) . '@' . self::_strpad($match[2]) . '.' . self::_strpad($match[3]);
    }

    static private function _strpad($string) {
        $return = "";
        if(mb_strlen($string) >= 3) {
            $return .=
                mb_substr($string, 0, 1) .
                str_pad('', mb_strlen($string) - 2, '*') .
                mb_substr($string, mb_strlen($string) - 1, 1)
            ;
        }
        else {
            $return .= str_pad('', mb_strlen($string), '*');
        }

        return $return;
    }
}