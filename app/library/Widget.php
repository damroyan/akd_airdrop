<?php
use Phalcon\Mvc\View;

class Widget extends Phalcon\DI\Injectable {

    /** @var View */
    private $_view;

    private $_array = [];

    public function __construct() {
    }

    public function initialize() {
    }

    public function __set($key, $value) {
        // dev cache
        if($key === 'cache') {
            $value = (!$this->config->application->production ? ($value > 10 ? 10 : $value) : $value);
        }

        $this->_array[$key] = $value;
        return $value;
    }

    public function getParams($key = null) {
        if(is_null($key)) {
            return $this->_array;
        }
        else {
            return $this->$key;
        }
    }

    public static function widget(array $params = []) {
        $class = get_called_class();

        /* @var $widget Widget */
        $widget = new $class();
        foreach ($params as $key => $value) {
            $widget->$key = $value;
        }
        $widget->initialize();

        return $widget->run();
    }

    /**
     * @return View
     */
    public function getView() {
        if ($this->_view === null) {
            $this->_view = $this->di->get('widgetView');
            $this->_view->setDi($this->di);
        }
        return $this->_view;
    }

    /**
     * @param View $view
     * @return Widget
     */
    public function setView(View $view) {
        $this->_view = $view;
        return $this;
    }

    /**
     * @param string $view_name
     * @param array  $params
     * @return string
     */
    public function render($view_name, array $params = []) {
        return $this->getView()->getRender('views', $view_name, $params);
    }

    /**
     * @return string
     */
    public function run() {
    }

    public function getRender($params = [], $view_name = null) {
        $e = explode('\\', get_called_class());

        return $this->render(
            $view_name ? $view_name : lcfirst(array_pop($e)),
            $params
        );
    }
}
