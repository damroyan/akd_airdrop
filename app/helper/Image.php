<?php
namespace Helper;

class Image {
    private $_width     = 0;
    private $_height    = 0;
    private $_path      = '';

    static private $_config = [
        'folder'        => 's/',
        'key'           => '',
        'imageDomain'   => '/',
        'blankDomain'   => '/',
        'blankImage'    => 'front/img/blank.png',
    ];

    protected static $instance;  // object instance

    private function __construct(){ /* ... @return Singleton */ }
    private function __clone()    { /* ... @return Singleton */ }

    /**
     * @return \Helper\Image
     */
    public static function getInstance() {
        if ( !isset(self::$instance) ) {
            $class = __CLASS__;
            self::$instance = new $class();

            return self::$instance;
        }
        return self::$instance;
    }

    public function setConfig($config = []) {
        self::$_config = array_merge(
            self::$_config,
            $config
        );

        return $this;
    }

    public function getConfig() {
        return self::$_config;
    }

    public function image($image, $width = null, $height = null, $method = 'crop', $type = 'jpg', $retina = false, $watermark = false, $postId = null) {
        if(preg_match('@^(\/\/|http)@ui', $image)) {
            $this->_path = $image;
            return $this;
        }

        if(preg_match('@^\/upload\/@ui', $image)) {
            $this->_path = $image;
            return $this;
        }

        if (
            !$image
            || ($width == 0 && $height == 0)
        ) {
            $this->_path = self::$_config['blankDomain'] . self::$_config['blankImage'];
            return $this;
        }

        $image = preg_replace('@\/+@ui', '/', $image);

        $this->_width 	= intval($width);
        $this->_height 	= intval($height);

        if($retina) {
            $this->_width = $this->_width * 2;
            $this->_height = $this->_height * 2;
        }

        switch($type) {
            case 'png':
                $type = 'png';
                break;

            default:
                $type = 'jpg';
                break;
        }

        switch($method) {
            case 'ratio':
                $_method = 9;
                break;

            case 'fb_share':
                $_method = 7;
                break;

            case '16x9':
                $_method = 6;
                break;

            case '1x1':
                $_method = 8;
                break;

            case 'icon':
                $_method = 5;
                break;

            case 'mwh':
                $_method = 4;
                break;

            case 'max':
                $_method = 3;
                break;

            case 'min':
                $_method = 2;
                break;

            case 'resize':
                $_method = 1;
                break;

            case 'crop':
            default:
                $_method = 0;
                break;
        }

        $checksum = crc32("{$image}:{$this->_width}:{$this->_height}:{$_method}:{$type}" . ($watermark ? ':1' : '') . ($postId ? ":$postId" : "") . ":" . self::$_config['key']);
        $crc = sprintf("%08.8s", sprintf("%x", $checksum));

        $image 	= str_replace('.', '___', $image);
        preg_match('@^\/(.*\/)?(.*___.*)$@ui', $image, $match);

        $this->_path = self::$_config['imageDomain'] . self::$_config['folder'];
        $this->_path .= "{$match[1]}{$this->_width}x{$this->_height}_{$match[2]}____{$_method}" . ($watermark ? '_1' : '') . ($postId ? "_p$postId" : "") . "_{$crc}.{$type}";

        return $this;
    }

    public function __toString() {
        return $this->_path;
    }

    public function html() {
        return $this->_path . (($this->_width > 0 || $this->_height > 0) ? '" style="' . ($this->_width > 0 ? 'width: ' . $this->_width . 'px; ' : '') . ($this->_height > 0 ? 'height: ' . $this->_height . 'px;' : '') : '');
    }

    public function path() {
        return $this->_path;
    }
}
