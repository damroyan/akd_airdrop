<?php
namespace Helper;

class String2Url {
    static private $_domain = [];

    static public function setDomain($domain, $type = 'www') {
        self::$_domain[$type] = $domain;
    }

    /**
     * Это URL
     *
     * @param $string
     */
    static public function isHttp($string) {
        $params = [
            'url' => $string,
        ];

        $validation = new \Phalcon\Validation();

        $validation->add(
            'url',
            new \UrlValidator([
                'allowEmpty' => false,
            ])
        );

        $messages = $validation->validate($params);
        if (count($messages)) {
            return false;
        }

        return true;
    }

    /**
     * Отображаем только домен из URL
     *
     * @param $string
     * @return string
     */
    static public function domain($string) {
        $string = self::url($string);

        preg_match('@^(.+)?tp(s)?://(www\.)?([^/]+)@ui', $string, $match);

        $e = $match[4] ? explode('?', $match[4]) : null;

        $domain = $match[4] ? $e[0] : $string;

        try {
            $domain = idn_to_utf8($domain);
        }
        catch(\Exception $e) {
        }

        return mb_strtolower($domain);
    }

    static public function removePrefix($string) {
        $string = preg_replace('@^(tel|mailto):@ui', '', $string);

        return "{$string}";
    }

    static public function url($string = "/", $type = 'www') {
        if(preg_match('@^(mailto|tel):@ui', $string)) {
            return $string;
        }
        elseif(preg_match('@^(ht|f)tp(s)?:@ui', $string)) {
            return $string;
        }
        elseif(preg_match('@^([0-9\-\(\)\s\+]+)$@ui', $string)) {
            $string = preg_replace('@(\(|\)|\-|\s)@', '', $string);

            return "tel:{$string}";
        }
        elseif(preg_match('@^//@ui', $string)) {
            return "http:{$string}";
        }
        elseif(preg_match('@^/@ui', $string)) {
            if(!self::$_domain[$type]) {
                if($type == 'www' || !self::$_domain['www']) {
                    return "invalide://domain_{$type}{$string}";
                }
                else {
                    return "http://" . self::$_domain['www'] . "{$string}";
                }
            }
            else {
                return "http://" . self::$_domain[$type] . "{$string}";
            }
        }
        elseif(filter_var($string, FILTER_VALIDATE_EMAIL)) {
            return "mailto:{$string}";
        }
        else {
            return "";
        }
    }
}