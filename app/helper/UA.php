<?php
namespace Helper;

/**
 * php version
 * $.browser
 * 
 * Class UA
 * @package Helper
 */
class UA {
    private $_ua = null;

    static private $_browser = [];

    public function __construct($ua = null) {
        // If an UA is not provided, default to the current browser UA.
        if(!$ua) {
            $ua = $_SERVER['HTTP_USER_AGENT'];
        }

        $this->_ua = mb_strtolower($ua);
    }

    private function _parseUA($ua) {
        $match = [];
        preg_match('/(edge)\/([\w.]+)/u', $ua, $match) ||
        preg_match('/(opr)[\/]([\w.]+)/u', $ua, $match) ||
        preg_match('/(chrome)[ \/]([\w.]+)/u', $ua, $match) ||
        preg_match('/(iemobile)[\/]([\w.]+)/u', $ua, $match) ||
        preg_match('/(version)(applewebkit)[ \/]([\w.]+).*(safari)[ \/]([\w.]+)/u', $ua, $match) ||
        preg_match('/(webkit)[ \/]([\w.]+).*(version)[ \/]([\w.]+).*(safari)[ \/]([\w.]+)/u', $ua, $match) ||
        preg_match('/(webkit)[ \/]([\w.]+)/u', $ua, $match) ||
        preg_match('/(opera)(?:.*version|)[ \/]([\w.]+)/u', $ua, $match) ||
        preg_match('/(msie) ([\w.]+)/u', $ua, $match);
        if(!$match[0] && mb_strpos('trident', $ua)) { preg_match('/(rv)(?::| )([\w.]+)/u', $ua, $match); }
        if(!$match[0] && mb_strpos('compatible', $ua)) { preg_match('/(mozilla)(?:.*? rv:([\w.]+)|)/u', $ua, $match); }
        if(!$match[0]) { $match = []; }

        $platform_match = [];
        preg_match('/(ipad)/u', $ua, $platform_match) ||
        preg_match('/(ipod)/u', $ua, $platform_match) ||
        preg_match('/(windows phone)/u', $ua, $platform_match) ||
        preg_match('/(iphone)/u', $ua, $platform_match) ||
        preg_match('/(kindle)/u', $ua, $platform_match) ||
        preg_match('/(silk)/u', $ua, $platform_match) ||
        preg_match('/(android)/u', $ua, $platform_match) ||
        preg_match('/(win)/u', $ua, $platform_match) ||
        preg_match('/(mac)/u', $ua, $platform_match) ||
        preg_match('/(linux)/u', $ua, $platform_match) ||
        preg_match('/(cros)/u', $ua, $platform_match) ||
        preg_match('/(playbook)/u', $ua, $platform_match) ||
        preg_match('/(bb)/u', $ua, $platform_match) ||
        preg_match('/(blackberry)/u', $ua, $platform_match);
        if(!$platform_match[0]) { $platform_match = []; }

        $browser = [];
        $matched = [
            'browser'       => ($match[5] ? $match[5] : ($match[3] ? $match[3] : ($match[1] ? $match[1] : ""))),
            'version'       => ($match[2] ? $match[2] : ($match[4] ? $match[4] : "")),
            'versionNumber' => ($match[4] ? $match[4] : ($match[2] ? $match[2] : "")),
            'platform'      => ($platform_match[0] ? $platform_match[0] : ""),
        ];

        if($matched['browser']) {
            $browser[$matched['browser']]   = true;
            $browser['version']             = $matched['version'];
            $browser['versionNumber']       = (int)$matched['versionNumber'];
        }

        if($matched['platform']) {
            $browser[$matched['platform']] = true;
        }

        // These are all considered mobile platforms, meaning they run a mobile browser
        if(
            $browser['android'] || $browser['bb'] || $browser['blackberry'] || $browser['ipad'] || $browser['iphone'] ||
            $browser['ipod'] || $browser['kindle'] || $browser['playbook'] || $browser['silk'] || $browser["windows phone"]
        ) {
            $browser['mobile'] = true;
        }

        // These are all considered desktop platforms, meaning they run a desktop browser
        if (
            $browser['cros'] || $browser['mac'] || $browser['linux'] || $browser['win']
        ) {
            $browser['desktop'] = true;
        }

        // Chrome, Opera 15+ and Safari are webkit based browsers
        if(
            $browser['chrome'] || $browser['opr'] || $browser['safari']
        ) {
            $browser['webkit'] = true;
        }

        // IE11 has a new token so we will assign it msie to avoid breaking changes
        if (
            $browser['rv'] || $browser['iemobile']
        ) {
            $ie = "msie";
            $matched['browser'] = $ie;
            $browser[$ie] = true;
        }

        // Edge is officially known as Microsoft Edge, so rewrite the key to match
        if($browser['edge']) {
          unset($browser['edge']);
          $msedge = "msedge";

          $matched['browser'] = $msedge;
          $browser[$msedge] = true;
        }

        // Blackberry browsers are marked as Safari on BlackBerry
        if($browser['safari'] && $browser['blackberry']) {
            $blackberry = "blackberry";

            $matched['browser'] = $blackberry;
            $browser[$blackberry] = true;
        }

        // Playbook browsers are marked as Safari on Playbook
        if ($browser['safari'] && $browser['playbook']) {
            $playbook = "playbook";

            $matched['browser'] = $playbook;
            $browser[$playbook] = true;
        }

        // BB10 is a newer OS version of BlackBerry
        if ($browser['bb']) {
            $bb = "blackberry";

            $matched['browser'] = $bb;
            $browser[$bb] = true;
        }

        // Opera 15+ are identified as opr
        if ($browser['opr']) {
            $opera = "opera";

            $matched['browser'] = $opera;
            $browser[$opera] = true;
        }

        // Stock Android browsers are marked as Safari on Android.
        if ( $browser['safari'] && $browser['android'] ) {
          $android = "android";

          $matched['browser'] = $android;
          $browser[$android] = true;
        }

        // Kindle browsers are marked as Safari on Kindle
        if ( $browser['safari'] && $browser['kindle'] ) {
          $kindle = "kindle";

          $matched['browser'] = $kindle;
          $browser[$kindle] = true;
        }

         // Kindle Silk browsers are marked as Safari on Kindle
        if ( $browser['safari'] && $browser['silk'] ) {
          $silk = "silk";

          $matched['browser'] = $silk;
          $browser[$silk] = true;
        }

        // Assign the name and platform variable
        $browser['name'] = $matched['browser'];
        $browser['platform'] = $matched['platform'];

        return $browser;
    }

    public function getBrowser() {
        if(!$this->_ua) {
            return [];
        }

        $hash = md5($this->_ua);

        if(!self::$_browser[$hash]) {
            self::$_browser[$hash] = $this->_parseUA($this->_ua);
        }

        return self::$_browser[$hash];
    }

    static public function Platform($ua = null) {
        $self = new self($ua);

        $browser = $self->getBrowser();
        switch ($browser['platform']) {
            case 'win':
                return 'is-win';
        		break;

            case 'android':
                return 'is-android';
        		break;

            case 'ipad':
            case 'ipod':
            case 'iphone':
        	case 'mac':
                return 'is-mac';
        		break;

            default:
                return '';
                break;
        }
    }

    static public function Parse($ua = null) {
        $self = new self($ua);

        return $self->getBrowser();
    }
}
