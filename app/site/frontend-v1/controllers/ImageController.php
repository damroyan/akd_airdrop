<?php
namespace Site\FrontendV1\Controllers;

/**
 * @property \Phalcon\Http\Response $response
 */
class ImageController extends Controller {
    public function initialize() {
    }

    /**
     * \Response c пустой PNG
     *
     * @Role({"allow": ['public']})
     * @return \Phalcon\Http\Response
     */
    private function _responseBlankPng() {
        $response = new \Phalcon\Http\Response();

        $response->setStatusCode(404, 'Not Found');
        $response->setHeader("Content-Type", "image/png");
        $response->setContent(
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVQYV2NgYAAAAAMAAWgmWQ0AAAAASUVORK5CYII==')
        );

        return $response;
    }

    /**
     * Response с картинкой
     *
     * @param $file
     * @param string $type
     * @return \Phalcon\Http\Response
     */
    private function _responseImage($file, $type = 'jpg') {
        if(is_file($file)) {
            $response = new \Phalcon\Http\Response();

            switch($type) {
                case 'png':
                    $response->setHeader("Content-Type", "image/png");
                    break;

                default:
                    $response->setHeader("Content-Type", "image/jpeg");
                    break;
            }

            $response->setHeader('Pragma', 'cache');
            $response->setHeader('Cache-Control', 'public, max-age=' . (30 * 24 * 60 * 60));
            $response->setExpires(new \Datetime('+1 Month'));

            $response->setContent(
                file_get_contents($file)
            );

            return $response;
        }

        return $this->_responseBlankPng();
    }

    private function _responseBlob($blob, $type = 'jpg') {
        $response = new \Phalcon\Http\Response();

        switch($type) {
            case 'png':
                $response->setHeader("Content-Type", "image/png");
                break;

            default:
                $response->setHeader("Content-Type", "image/jpeg");
                break;
        }

        $response->setContent(
            $blob
        );

        return $response;
    }

    /**
     * Создание папок
     *
     * @param $uri
     * @param null $root
     * @return $this
     */
    private function _makeDirectory($uri, $root = null) {
        if(is_null($root)) {
            $root = DOCUMENT_ROOT;
        }

        $array = explode("/", $uri);
        $dir = '/';

        for ($i = 1; $i < sizeof($array) - 1; $i++) {
            if ($array[$i]) {
                $dir .= $array[$i] . '/';

                if (!is_dir("{$root}{$dir}")) {
                    mkdir("{$root}{$dir}");
                }
            }
        }

        return $this;
    }

    private function _gdText($text, $fontSize, $width, $font, $align = 'center') {
        $im = imagecreate($width, $fontSize * 1.8);
        imagealphablending($im, false);
        imagesavealpha($im, true);
        imagecolortransparent($im, imagecolorallocate($im, 0, 0, 0));

        $p = imagettfbbox(
            $fontSize,
            0,
            $font,
            html_entity_decode($text)
        );
        $textWidth= $p[2] - $p[0];

        switch($align) {
            case 'right':
                $x = ($width - $textWidth);
                break;

            case 'center':
                $x = ($width - $textWidth) / 2;
                break;

            case 'left':
            default:
                $x = 0;
                break;
        }

        imagettftext(
            $im,
            $fontSize, 0,
            $x,
            ($fontSize * 1.4),
            imagecolorallocate($im, 255, 255, 255),
            $font,
            html_entity_decode($text)
        );

        ob_clean();
        imagepng($im);
        $png = ob_get_contents();
        imagedestroy($im);
        ob_clean();

        return $png;
    }

    /**
     * Генерация картинки
     *
     * @Role({"allow": ['public']})
     */
    public function indexAction() {
        $params = $this->dispatcher->getParams();
        $params['filename'] = preg_replace('@___([^_]+)$@ui', '.$1', $params['filename']);


        $checksum = crc32(
            "{$params['path']}{$params['filename']}:{$params['width']}:{$params['height']}:{$params['method']}:{$params['ext']}" .
            ($params['watermark'] ? ':1' : '') .
            ($params['post_id'] ? ":{$params['post_id']}" : '') .
            ":{$this->config->system->imageKey}"
        );
        $crc = sprintf("%08.8s", sprintf("%x", $checksum));

        $file = DOCUMENT_ROOT . "{$params['path']}{$params['filename']}";

        /*
         * Почему-то перестало работать после переноса, потом надо разобраться
         * print_r($params);
        echo $crc.'!='.$params['crc'];
        exit;*/
        if(
           // $crc != $params['crc'] ||
            !preg_match('@\.(png|gif|jpg|jpeg)$@ui', $params['filename'])
            || !is_file($file)

        ) {
            return $this->_responseBlankPng();
        }
        $uri = preg_replace('@\/+@ui', '/', explode('?', $_SERVER['REQUEST_URI'])[0]);

        $this->_makeDirectory($uri);
        try {

            $image = new \Image($file);
            if(!$image->isImage) {
                throw new \Exception("File: {$file} is not image");
            }

            switch($params['ext']) {
                case 'jpg':
                    switch($params['method']) {
                        case '7':
                            break;

                        default:
                            $image->setBackground('white');
                            break;
                    }
                    break;

                default:
                    break;
            }

            switch($params['method']) {
                case '0':
                    $image->crop($params['width'], $params['height']);
                    break;

                case '1':
                    $image->resize($params['width'], $params['height']);
                    break;

                case '2':
                    $image->resizeMin($params['width'], false);
                    break;

                case '3':
                    $image->resizeMax($params['width'], false);
                    break;

                case '4':
                    $image->resizeMaxWidthHeight($params['width'], $params['height'], false);
                    break;

                case '5':
                    $border = (new \Image())->blank();
                    $border->im->newImage($params['width'], $params['width'], new \ImagickPixel('#007fcd'));
                    $border->setWidthHeightRatio();
                    $border->round($params['width'] / 2);

                    $width = $params['width'] - 4;

                    $white = (new \Image())->blank();
                    $white->im->newImage($width, $width, new \ImagickPixel('#ffffff'));
                    $white->setWidthHeightRatio();
                    $white->round($width / 2);
                    $border->im->compositeImage(
                        $white->im,
                        \Imagick::COMPOSITE_ATOP,
                        ($border->im - $white->width) / 2,
                        ($border->im - $white->height) / 2
                    );

    //                $image->setBackground('#ffffff');
                    $image->resizeMin($width, false);
                    $image->crop($width, $width,  0);
                    $image->round($width / 2);

                    $border->im->compositeImage(
                        $image->im,
                        \Imagick::COMPOSITE_ATOP,
                        ($border->width - $image->width) / 2,
                        ($border->height - $image->height) / 2
                    );
                    $image = $border;

                    break;

                // 16x9
                case '6':
                    $image->resizeRatio(16 / 9, $params['width']);
                    break;

                // 1200 x 630
                // FB Share
                case '7':
                    $padding = 40;

                    // Картинка сверху
                    $shareImage = (new \Image())->blank();
                    $shareImage->im->newImage($params['width'], ($params['width'] * 630 / 1200), new \ImagickPixel('#191919'), 'jpg');
                    $shareImage->setWidthHeightRatio();

                    if($image->ratio > $params['width'] / 630) {
                        $image->im->scaleImage(630 * $image->ratio, 630, true);
                    }
                    else {
                        $image->im->scaleImage($params['width'], $params['width'] / $image->ratio, true);
                    }
                    $image->setWidthHeightRatio();

                    $image->im->blurImage(48, 48);
                    $image->im->modulateImage(100, 120, 100);

                    $shareImage->im->compositeImage(
                        $image->im,
                        \Imagick::COMPOSITE_ATOP,
                        ($shareImage->width - $image->width) / 2,
                        ($shareImage->height - $image->height) / 2
                    );

                    // Затемнение
                    $layerImage = (new \Image())->blank();
                    $layerImage->im->newImage($params['width'], ($params['width'] * 630 / 1200), new \ImagickPixel('#191919'), 'jpg');
                    $layerImage->setWidthHeightRatio();
                    $layerImage->im->setImageOpacity(0.6);

                    $shareImage->im->compositeImage(
                        $layerImage->im,
                        \Imagick::COMPOSITE_ATOP,
                        ($shareImage->width - $layerImage->width) / 2,
                        ($shareImage->height - $layerImage->height) / 2
                    );

                    // Лого
                    $logotypeImage = (new \Image(DOCUMENT_ROOT . '/front/b-header/logotype-white-opacity.png'));
                    $shareImage->im->compositeImage(
                        $logotypeImage->im,
                        \Imagick::COMPOSITE_ATOP,
                        $padding,
                        $padding
                    );

                    $post = \Model\Post::findFirst([
                        'post_id = :post_id:',
                        'bind' => [
                            'post_id' => $params['post_id'],
                        ],
                    ]);

                    if($post) {
                        $header = null;
                        if($post->post_category_id) {
                            $categoryBranch = \Model\PostCategory::getBranch($post->post_category_id);
                            $categoryRoot = $categoryBranch[count($categoryBranch) - 1];
                            $header = $categoryRoot['post_category_name'];
                        }

                        if($header) {
                            // line
                            $draw = new \ImagickDraw();
                            $draw->setStrokeColor(new \ImagickPixel('#ffffff'));
                            $draw->setFillColor(new \ImagickPixel('#ffffff'));
                            $draw->setStrokeWidth(2);
                            $draw->line(($padding * 1.5 + $logotypeImage->width), $padding, ($padding * 1.5 + $logotypeImage->width), ($padding + $logotypeImage->height));
                            $shareImage->im->drawImage($draw);

                            $png = $this->_gdText(
                                $header,
                                24,
                                ($shareImage->width - ($padding + $logotypeImage->width) - 2 * $padding),
                                DOCUMENT_ROOT . '/front/font/PTSans.ttc',
                                'left'
                            );

                            $textLayer = (new \Image())->blank();
                            $textLayer->im->readImageBlob($png);
                            $textLayer->setWidthHeightRatio();

                            $shareImage->im->compositeImage(
                                $textLayer->im,
                                \Imagick::COMPOSITE_ATOP,
                                ($padding * 2 + $logotypeImage->width),
                                $padding + ($logotypeImage->height - $textLayer->height) / 2
                            );
                        }

                        // Текст
                        if($post->post_name) {
                            $array = [];
                            $i = 0;
                            $l = 0;
                            foreach(explode(' ', $post->post_name) as $item) {
                                $item = trim($item);

                                if($item) {
                                    $li = mb_strlen($item);
                                    if($li + $l > 36) {
                                        $i++;
                                        $l = 0;

                                        if($i >= 4) {
                                            $n = count($array[$i - 1]) - 1;
                                            $array[$i - 1][$n] = $array[$i - 1][$n] . '&hellip;';
                                            break;
                                        }
                                    }
                                    $l += $li + 1;

                                    $array[$i][] = $item;
                                }
                            }

                            $l = count($array);
                            foreach($array as $line => $item) {
                                $png = $this->_gdText(
                                    implode(' ', $item),
                                    42,
                                    ($shareImage->width - $padding * 2),
                                    DOCUMENT_ROOT . '/front/font/PTSans.ttc'
                                );

                                $textLayer = (new \Image())->blank();
                                $textLayer->im->readImageBlob($png);
                                $textLayer->setWidthHeightRatio();

                                $shareImage->im->compositeImage(
                                    $textLayer->im,
                                    \Imagick::COMPOSITE_ATOP,
                                    ($shareImage->width - $textLayer->width) / 2,
                                    ($shareImage->height - $textLayer->height * $l) / 2 + $textLayer->height * $line
                                );
                            }
                        }
                    }

                    $image = $shareImage;

//                    return $this->_responseBlob(
//                        (string)$image->im
//                    );

                    break;

                // 1x1
                case '8':
                    $image->resizeRatio(1 / 1, $params['width']);
                    break;

                case '9':
                    $image->resizeRatio($params['width'] / $params['height'], $params['width']);
                    break;

                default:
                    return $this->_responseBlankPng();
                    break;
            }

            if($params['watermark']) {
                $image->setWatermark(DOCUMENT_ROOT . "/front/gd/logotype.png");
            }

            $image->save(DOCUMENT_ROOT . $uri);
            return $this->_responseImage(DOCUMENT_ROOT . $uri, $params['ext']);
        }
        catch(\Exception $e) {
            var_dump($e);
            exit();
        }
    }
}
