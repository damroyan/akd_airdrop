<?php
namespace Helper;

/**
 * Работа с массивом для upload картинок
 *
 * Class Upload
 * @package Helper
 */
class Upload {
    private $_config = array();

    private $_dir = null;
    private $_count = 0;

    private $_exist            = null;
    private $_upload           = null;
    private $_delete           = null;
    private $_deleteComplete   = null;
    private $_move             = null;
    private $_other            = null;

    public function __construct($config = null) {
        if($config) {
            $this->setConfig($config);
        }

        return $this;
    }

    /**
     * Массив файла конфигурации
     *
     * @param $config
     * @return $this
     */
    public function setConfig($config) {
        if(is_array($config)) {
            $this->_config = $config;
        }

        return $this;
    }

    /**
     * Установка папки для upload (путь относительно DOCUMENT_ROOT)
     *
     * @param $dir
     * @return $this
     */
    public function setDirectory($dir) {
        if(is_dir(DOCUMENT_ROOT . $dir)) {
            $this->_dir = $dir;
        }

        return $this;
    }

    /**
     * Создание папок
     *
     * @param $uri
     * @param null $root
     * @return $this
     */
    static public function makeDirectory($uri, $root = null) {
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
    }

    /**
     * Копирование картинки
     *
     * @param $source - источник
     * @param $direct - куда сохранить
     * @param bool $unlink_source - удалить источник
     * @param bool $overwrite - переписать файл
     * @return bool
     * @throws \Exception
     */
    private function _copy($source, $direct, $unlink_source, $overwrite) {
        if(!is_file($source)) {
            throw new \Exception("Copy: File 'source' {$source} no exist");
        }

        if(is_file($direct)) {
            if($overwrite) {
                unlink($direct);
            }
            else {
                throw new \Exception("Copy: File 'to' {$direct} exist");
            }
        }

        copy($source, $direct);

        if(is_file($source) && $unlink_source) {
            unlink($source);
        }

        return true;
    }

    /**
     * Считаем итоговое кол-во картинок
     *
     * @return int
     */
    public function count($new = false) {
        $move   = is_array($this->_move) ? count($this->_move) : 0;
        if($new) {
            return $move;
        }

        $exist  = is_array($this->_exist) ? count($this->_exist) : 0;
        $delete = is_array($this->_deleteComplete) ? count($this->_deleteComplete) : 0;

        $count = $exist + $move - $delete;

        return $count > 0 ? $count : 0;
    }

    /**
     * Перенос картинки из временной папки в постоянную
     *
     * @throws \Exception
     */
    public function commit($unlink = false, $unlink_source = true, $overwrite = true) {
        if(is_array($this->_move)) {
            foreach($this->_move as $path) {
                if(!is_file(DOCUMENT_ROOT . $path['source'])) {
                    continue;
                }

                self::makeDirectory($path['direct']);

                $this->_copy(
                    DOCUMENT_ROOT . $path['source'],
                    DOCUMENT_ROOT . $path['direct'],
                    $unlink_source,
                    $overwrite
                );
            }
        }

        if($unlink) {
            if(!is_array($this->_deleteComplete)) {
                $this->delete(function($id) {
                    return true;
                });
            }

            foreach($this->_deleteComplete as $value) {
                if(is_file(DOCUMENT_ROOT . $value['value'])) {
                    unlink(DOCUMENT_ROOT . $value['value']);
                }
            }
        }

        return true;
    }

    /**
     * Получени папки для сохранения итоговой картинки
     *
     * @param $path
     * @return string
     */
    private function _getPath($path) {
        if(is_null($this->_dir)) {
            $this->_dir = $this->_config['application']['imageAdvHelpDir'];
        }

        $md5 = md5($path . microtime(true));

        preg_match('@\.([a-z]{3,4})$@ui', $path, $match);
        $ext = $match[1];

        preg_match('@^([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{1})(.*)?([0-9a-f]{6})$@ui', $md5, $match);

        return "{$this->_dir}/{$match[1]}/{$match[2]}/{$md5}.{$ext}";
    }

    /**
     * callback для сохранение пути картинки в БД
     *
     * @param $callback
     * @return $this
     */
    public function save($callback) {
        if(is_array($this->_upload)) {
            foreach($this->_upload as $source) {
                $direct = $this->_getPath($source);
                if($callback($direct)) {
                    $this->_move[] = array(
                        'source'    => $source,
                        'direct'    => $direct,
                    );
                }
            }
        }

        return $this;
    }

    private function _createDeleteArray() {
        // Нахождение расхождения между картинками которые уже загружены и тем что загружается
        $exist = [];
        $uploaded = [];

        if(is_array($this->_exist)) {
            foreach($this->_exist as $value) {
                $exist[] = $value['value'];

                if(is_array($this->_other)) {
                    if(in_array($value['value'], $this->_other)) {
                        $uploaded[] = $value['value'];
                    }
                }
            }
        }

        $diff = array_diff($exist, $uploaded);

        if(!is_array($this->_delete)) {
            $this->_delete = [];
        }

        if(is_array($this->_exist)) {
            foreach ($this->_exist as $value) {
                if(in_array($value['value'], $diff)) {
                    $this->_delete[
                        $value['id']
                    ] = $value;
                }
            }
        }
    }

    /**
     * callback для удаление картинки в БД
     *
     * @param $callback
     * @return $this
     */
    public function delete($callback) {
        if(!is_array($this->_deleteComplete)) {
            $this->_deleteComplete = [];
        }

        if(!is_array($this->_delete)) {
            $this->_createDeleteArray();
        }

        if(is_array($this->_delete)) {
            foreach($this->_delete as $value) {
                if($callback($value['id'])) {
                    $this->_deleteComplete[
                        $value['id']
                    ] = $value;
                }
            }
        }

        return $this;
    }

    /**
     * Обработка массива картинок которые пришли из $_POST
     *
     * @param $array
     * @return $this
     */
    public function setUpload($array) {
        if(!is_array($array)) {
            $array = [
                $array,
            ];
        }

        if(!is_array($this->_upload)) {
            $this->_upload = [];
        }

        if(!is_array($this->_other)) {
            $this->_other = [];
        }

        foreach($array as $file) {
            if(preg_match('@^' . $this->_config['application']['imageTempDir'] . '/([0-9a-f]{32})\.(jpg|png|gif)$@ui', $file)) {
                if(is_file(DOCUMENT_ROOT . $file)) {
                    list($status, $error_code, $ext) = \Image::Validate(DOCUMENT_ROOT . $file);
                    if($status === true) {
                        if(!in_array($file, $this->_upload)) {
                            $this->_upload[] = $file;
                        }
                    }
                }
            }
            else {
                if(!in_array($file, $this->_other)) {
                    $this->_other[] = $file;
                }
            }
        }

        return $this;
    }

    /**
     * Обработка массива текущих картинок из БД
     *
     * @param $a1
     * @param array $map
     * @return $this
     */
    public function setExist($array, $map = array()) {
        if(!is_array($this->_exist)) {
            $this->_exist = [];
        }

        if(!is_array($array)) {
            $a1 = [
                [
                    'id'    => $this->_count,
                    'value' => $array,
                ],
            ];

            $this->_count++;
        }
        elseif($array[($map['id'] ? $map['id'] : 'id')]) {
            $a1 = [
                $array
            ];
        }
        else {
            $a1 = $array;
        }

        foreach($a1 as $value) {
            $this->_exist[
                $value[($map['id'] ? $map['id'] : 'id')]
            ] = [
                'id'    => $value[($map['id'] ? $map['id'] : 'id')],
                'value' => $value[($map['value'] ? $map['value'] : 'value')],
            ];
        }

        return $this;
    }
}
