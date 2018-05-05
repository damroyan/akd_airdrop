<?php
namespace Helper;

class DbIn {
    static private $_prefix_used = [];

    static private function _getPrefixId() {
        $p = null;
        $state = false;
        while(!$state) {
            $p = rand(1000, 9999);

            if(!self::$_prefix_used[$p]) {
                $state = true;
                self::$_prefix_used[$p] = $p;
            }
        }

        return $p;
    }

    static public function exec($array, $prefix = null, $pdo = false) {
        if(is_null($prefix)) {
            $p = self::_getPrefixId();

            $prefix = "prfx_in_{$p}";
        }

        $placeholder = array();
        $value = array();

        $i = 0;
        foreach($array as $v) {
            $placeholder[$pdo ? ":prfx_in_{$prefix}_{$i}" : ":prfx_in_{$prefix}_{$i}:"] = true;
            $value[($pdo ? ":" : '') . "prfx_in_{$prefix}_{$i}"] = $v;

            $i++;
        }

        return array(implode(', ', array_keys($placeholder)), $value, $array);
    }
}