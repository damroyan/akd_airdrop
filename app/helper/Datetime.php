<?php
namespace Helper;

class Datetime {
    static private $_arrayMonthRus = [
        '',
        'января', 'февраля', 'марта', 'апреля', 'мая', 'июня',
        'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря',
    ];

    static private $_arrayWeekdayShortRus = [
        'Вс',
        'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс',
    ];

    static private $_arrayWeekdayRus = [
        'Воскресенье',
        'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота', 'Воскресенье',
    ];

    /**
     * Проверка даты
     *
     * @param $date1
     * @param string $date2
     * @param string $condition
     * @return bool
     */
    static public function validateDate($date1, $date2 = 'today', $condition = '>=') {
        $date1 = date('Y-m-d 00:00:00', strtotime($date1));
        $date2 = date('Y-m-d 00:00:00', strtotime($date2));

        switch($condition) {
            case '>=':
                return $date1 >= $date2 ? true : false;
                break;

            case '>':
                return $date1 > $date2 ? true : false;
                break;

            case '==':
                return $date1 == $date2 ? true : false;
                break;

            case '<=':
                return $date1 <= $date2 ? true : false;
                break;

            case '<':
                return $date1 < $date2 ? true : false;
                break;
        }

        return false;
    }

    static public function isTimestamp($value) {
        $timestamp = self::exec($value, 'timestamp');

        if($timestamp > 0) {
            return true;
        }
        else {
            return false;
        }
    }

    static public function check($value, $period = null, $direction = '<') {
        $timestamp = self::exec($value, 'timestamp');
        $time = ($period ? strtotime($period) : time()) - time();

        if($direction == '<') {
            return ($timestamp + $time) < time()  ? true : false;
        }
        else {
            return ($timestamp + $time) > time() ? true : false;
        }
    }

    static public function exec($string, $result = 'datetime') {
        if(!$string) {
            $value = time();
        }
        else if(preg_match('@^(\d{2})\.(\d{2})\.(\d{4}) (\d{2}):(\d{2}):(\d{2})$@ui', $string, $match)) {
            $value = strtotime("{$match[3]}-{$match[2]}-{$match[1]} {$match[4]}:{$match[5]}:{$match[6]}");
        }
        elseif(preg_match('@^\d{1,10}$@ui', $string)) {
            $value = $string;
        }
        else {
            $value = strtotime($string);
        }

        switch($result) {
            case 'check':
                if(!$string || $string == '0000-00-00 00:00:00') {
                    return false;
                }
                else {
                    return true;
                }

                break;

            case 'time_hm':
                return date('H:i', $value);
                break;

            case 'utc':
                return date('c', $value);

                break;

            case 'dmr':
                $str = date('j', $value);
                $str .= " " . self::$_arrayMonthRus[date('n', $value)];
                $str .= " " . date('Y', $value);
                return $str;

                break;

            case 'dmr_pretty':
                $today = date('Y-m-d');
                $yesterday = date('Y-m-d', strtotime('-1 day'));

                switch(date('Y-m-d', $value)) {
                    case $today:
                        return 'Сегодня';

                        break;

                    case $yesterday:
                        return 'Вчера';

                        break;

                    default:
                        $week = strtotime(date('Y-m-d 23:59:59')) - 7 * 24 * 60 * 60;
                        $diff = $value - $week;

                        if($diff > 0 && $value <= time()) {
                            return self::$_arrayWeekdayRus[date('N', $value)];
                        }
                        elseif($value > time()) {
                            return date('j', $value) . " " . self::$_arrayMonthRus[date('n', $value)] .
                                (" " . date('Y', $value));
                        }
                        else {
                            return date('j', $value) . " " . self::$_arrayMonthRus[date('n', $value)] .
                                (date('Y') != date('Y', $value) ? (" " . date('Y', $value)) : " ");
                        }

                        break;
                }

                break;

            case 'sql':
                $str = date('Y-m-d H:i:s', $value);
                if(preg_match('@^\-@ui', $str)) {
                    $str = '0000-00-00 00:00:00';
                }
                return $str;

                break;

            case 'pretty':
                $today = date('Y-m-d');
                $yesterday = date('Y-m-d', strtotime('-1 day'));

                switch(date('Y-m-d', $value)) {
                    case $today:
                        $diff = time() - $value;
                        if($diff >= 0 && $diff <= 60 * 60 ) {
                            $minute = ceil($diff / 60);

                            if($minute > 1) {
                                return $minute . " " . \Helper\Text::plural($minute, [
                                    'минут',
                                    'минута',
                                    'минуты'
                                ]) . " назад";
                            }
                            else {
                                return 'только что';
                            }
                        }
                        else {
                            return date('H:i', $value) . ', Сегодня';
                        }

                        break;

                    case $yesterday:
                        return date('H:i', $value) . ', Вчера';

                        break;

                    default:
                        $week = strtotime(date('Y-m-d 23:59:59')) - 7 * 24 * 60 * 60;
                        $diff = $value - $week;

                        if($diff > 0 && $value <= time()) {
                            return date('H:i', $value) . ', ' . self::$_arrayWeekdayRus[date('N', $value)];
                        }
                        elseif($value > time()) {
                            return date('j', $value) . " " . self::$_arrayMonthRus[date('n', $value)] .
                                (" " . date('Y', $value));
                        }
                        else {
                            return date('j', $value) . " " . self::$_arrayMonthRus[date('n', $value)] .
                                (date('Y') != date('Y', $value) ? (" " . date('Y', $value)) : " ");
                        }

                        break;
                }

                break;

            case 'sql_date':
                $str = date('Y-m-d', $value);
                if(preg_match('@^\-@ui', $str)) {
                    $str = '0000-00-00';
                }
                return $str;

                break;

            case 'year':
                return date('Y', $value);

                break;

            case 'time':
                return date('H:i:s', $value);

                break;

            case 'date':
                return date('d.m.Y', $value);

                break;

            case 'datetime':
                return date('d.m.Y H:i:s', $value);

                break;

            case 'timestamp':
                return $value;

                break;
        }

        return '';
    }
}
