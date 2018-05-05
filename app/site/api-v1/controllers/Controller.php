<?php
namespace Site\ApiV1\Controllers;

class Controller extends \Controller {

    public function initialize() {
        // something that will works for whole module
    }

    /**
     * @param \Phalcon\Validation|array $validator
     * @param array $params
     * @param callable $callback
     * @return \Phalcon\Http\Response
     */
    final protected function validation($validators, $params = [], $callback = null) {
        if(!is_array($validators)) {
            $validators = [
                $validators,
            ];
        }

        foreach($validators as $validator) {
            /* @var \Phalcon\Validation $validator */
            $messages = $validator->validate($this->existParams($params));
            foreach($messages as $message) {
                \Response::FieldsAdd($message->getField(), $message->getMessage());
            }
        }

        if(!is_null($callback)) {
            $callback($params);
        }

        if(\Response::FieldsError()) {
            return \Response::Error('400');
        }

        return true;
    }

    final protected function error403() {
        return $this->dispatcher->forward([
            'namespace'     => 'Site\ApiV1\Controllers',
            'controller'    => 'error',
            'action'        => 'forbidden',
        ]);
    }

    final protected function error404() {
        return $this->dispatcher->forward(array(
            'namespace'     => 'Site\ApiV1\Controllers',
            'controller'    => 'error',
            'action'        => 'notfound',
        ));
    }

    /**
     * Получение $_POST параметров. Если debug, то $_GET
     *
     * @return mixed|\Phalcon\Http\Response
     */
    protected function getParams() {
        $debug = $this->request->getQuery('debug');
        if(isset($debug)) {
            $params = $this->request->getQuery();
            unset($params['_url']);
            unset($params['debug']);
        }
        else {
            $params = $this->request->getPost();

            if(!$this->request->isPost()) {
                return \Response::Error(400, 'method_post');
            }
        }

        return $this->trimParams($params);
    }
}
