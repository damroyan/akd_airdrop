<?php
namespace Helper;

class Text {

    static public function cache($array = [], $ext = '.cache') {
        if(!is_array($array)) {
            $array = [$array, ];
        }

        foreach($array as $item) {
            preg_match_all('@[a-z0-9]+@ui', $item, $matches);

            var_dump($matches);
        }

    }

    static public function random() {
        return rand(1, 4294967295);
    }

    static public function plural($n, $forms) {
        $n = abs($n) % 100;
        $n1 = $n % 10;

        if ($n > 10 && $n < 20) {
            return $forms[0];
        }
        else if ($n1 > 1 && $n1 < 5) {
            return $forms[2] ? $forms[2] : $forms[0];
        }
        else if ($n1 == 1) {
            return $forms[1] ? $forms[1] : $forms[0];
        }
        else {
            return $forms[0];
        }
    }

    static public function hashNumber($string) {
        $number = 0;
        for($i = 0; $i < mb_strlen((string)$string); $i++) {
            if($number > 2048) {
                break;
            }

            $number += ord(mb_substr((string)$string, $i, 1));
        }

        return $number;
    }

    static public function removeHtml($string) {
        $string = htmlspecialchars_decode($string);

        $string = preg_replace('@<[^>]+>@ui', '', $string);
        $string = preg_replace('@&nbsp;@ui', ' ', $string);
        $string = preg_replace('@\s+@ui', ' ', $string);

        return trim($string);
    }

    static public function substr($string, $length = 256) {
        $string = self::removeHtml($string);

        if(is_null($length)) {
            return $string;
        }

        $string = mb_substr($string, 0, $length * 4);

        $explode = explode(" ", $string);
        $a = [];
        $l = 0;
        $hellip = false;

        foreach($explode as $value) {
            if($l >= $length) {
                $hellip = true;
                break;
            }

            $value = trim($value);

            if(!$value) {
                continue;
            }

            $a[] = $value;
            $l += mb_strlen($value);
        }

        return implode(' ', $a) . ($hellip ? '…' : '');
    }

    static public function preview($string, $length = 256) {
        $string = self::removeHtml($string);

        if(is_null($length)) {
            return $string;
        }

        $string = mb_substr($string, 0, $length * 8);

        $chars = preg_split('//ui', $string, -1);
        $a = [];

        foreach($chars as $k => $value) {
            $a[] = $value;

            if($k + 1 > $length && in_array($value, ['.', '?', '!', ])) {
                break;
            }
        }

        return implode('', $a);
    }

}
