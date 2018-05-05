<?php
namespace Site\FrontendV1\Controllers;

class FeedbackController extends Controller {

    public function initialize() {
        parent::initialize();

        $this->view->setVar('active_tab','feedback_');
    }

    /**
     * @Role({"allow": ['public']})
     */
    public function formAction() {


        $this->feedback = new \Model\Feedback();

        $this->view->setVar('feedback',$this->feedback);
    }
}