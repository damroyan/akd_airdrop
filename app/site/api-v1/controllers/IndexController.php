<?php
namespace Site\ApiV1\Controllers;

class IndexController extends Controller {
    public function initialize() {
        parent::initialize();
    }

    /**
     * Default response => Ok
     *
     * @Role({"allow": ['public']})
     * @return \Phalcon\Http\Response
     */
	public function indexAction() {
        return \Response::Ok(true);
    }

    /**
     * Результаты поиска. Все что хотим найти фигарим сюда
     *
     * @Role({"allow": ['public']})
     *
     */
    public function searchAction() {
        $q = $this->request->getQuery('q');

        $results    = [];
        if ($q) {
            // Todo
            // все что мы должны делать с запросом, делаем здесь
        }

        return \Response::Ok([
            'q'         => $q,
            'results'   => $results,
        ]);
    }

}
