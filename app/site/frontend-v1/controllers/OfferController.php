<?php
namespace Site\FrontendV1\Controllers;

class OfferController extends Controller
{
    public function initialize()
    {
        parent::initialize();
    }

    /**
     * @Role({"allow": ['public']})
     */
    public function oneAction()
    {

        $this->view->setVar('active_tab', 'index');

    }
}
