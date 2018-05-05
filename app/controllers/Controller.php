<?php
class Controller extends \Phalcon\Mvc\Controller {

    /**
     * Загрузка картинки
     *
     * @param $object
     * @param $params
     * @return \Helper\Upload|null
     */
    final protected function uploadSingleImage($object, $params, $key, $directory = null) {
        $upload = null;
        if($object->{$key} != $params[$key]) {
            $array = [];
            $array[$key] = null;

            $upload = (new \Helper\Upload($this->config->toArray()));

            if(!is_null($directory)) {
                $upload->setDirectory($directory);
            }

            if($object->{$key}) {
                $upload->setExist($object->{$key});
            }

            $object->assign($array);

            if($params[$key]) {
                $upload->setUpload($params[$key]);
            }

            $upload->save(function($path) use ($object, $key) {
                $array = [];
                $array[$key] = $path;

                $object->assign($array);

                return true;
            });
        }

        return $upload;
    }

    /**
     * Пробегаем по параметрам и делаем trim
     *
     * @param $params
     * @return mixed
     */
    protected function trimParams($params) {
        foreach($params as $key => $value) {
            if(is_array($value)) {
                $params[$key] = $this->trimParams($value);
            }
            else {
                $params[$key] = (string)trim($value);
            }
        }

        return $params;
    }

    protected function existParams($params) {
        $array = array();

        foreach($params as $key => $value) {
            if($value || (string)$value === '0') {
                $array[$key] = $value;
            }
        }

        return $array;
    }

}
