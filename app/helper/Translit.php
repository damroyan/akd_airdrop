<?php
namespace Helper;

class Translit {
    static private $_converter = array(
        'а' => 'a',   'б' => 'b',   'в' => 'v',
        'г' => 'g',   'д' => 'd',   'е' => 'e',
        'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
        'и' => 'i',   'й' => 'y',   'к' => 'k',
        'л' => 'l',   'м' => 'm',   'н' => 'n',
        'о' => 'o',   'п' => 'p',   'р' => 'r',
        'с' => 's',   'т' => 't',   'у' => 'u',
        'ф' => 'f',   'х' => 'h',   'ц' => 'c',
        'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
        'ь' => '\'',  'ы' => 'y',   'ъ' => '\'',
        'э' => 'e',   'ю' => 'yu',  'я' => 'ya',

        'А' => 'A',   'Б' => 'B',   'В' => 'V',
        'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
        'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
        'И' => 'I',   'Й' => 'Y',   'К' => 'K',
        'Л' => 'L',   'М' => 'M',   'Н' => 'N',
        'О' => 'O',   'П' => 'P',   'Р' => 'R',
        'С' => 'S',   'Т' => 'T',   'У' => 'U',
        'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
        'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
        'Ь' => '\'',  'Ы' => 'Y',   'Ъ' => '\'',
        'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
    );

    static private function _converter($str) {
        return strtr(trim($str), self::$_converter);
    }

    /**
     * Транслит Rus -> Eng
     *
     * @param $str
     * @return string
     */
    static public function str($str) {
        return self::_converter($str);
    }

    static public function code($str) {
        if(preg_match('@^[a-z0-9\-]+$@ui', $str)) {
            return true;
        }

        return false;
    }

    /**
     * Транслит формат URL
     *
     * @param $string
     * @param integer $length
     * @return mixed|string
     */
    static public function url($string, $length = 32) {
        $string = self::_converter($string);
        $string = mb_strtolower($string);

        preg_match_all('@[a-z0-9\-]+@ui', $string, $matches);

        if(is_null($length)) {
            $string = implode('-', $matches[0]);
        }
        else {
            $l = 0;
            $a = [];

            foreach($matches[0] as $value) {
                if($value) {
                    $l += mb_strlen($value);
                    $a[] = $value;
    
                    if($l > $length) {
                        break;
                    }
                }
            }

            $string = implode('-', $a);
        }

        return trim(preg_replace('@\-+@', '-', $string), '-');
    }
}
