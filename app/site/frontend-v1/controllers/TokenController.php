<?php
namespace Site\FrontendV1\Controllers;

class TokenController extends Controller {

    public function initialize()
    {
        parent::initialize();
    }

    /**
     * @Role({"allow": ['public']})
     */
    public function indexAction() {
        $params = $this->request->getQuery();

        $actionToken = null;
        if($params['action_token_id']) {
            $actionToken = \Model\ActionToken::findFirst([
                'action_token_id = :action_token_id:',
                'bind' => [
                    'action_token_id' => $params['action_token_id'],
                ]
            ]);
        }

        if(!$actionToken) {
            return $this->error404();
        }

        if(
            $actionToken->action_token_status != \Model\ActionToken::STATUS_ACTIVE ||
            strtotime($actionToken->action_token_edate) <= time()
        ) {
            $this->response->setStatusCode(400, 'Bad Request');
        }

        $this->view->action_token = $actionToken;

        if($actionToken) {
            \Phalcon\Tag::setDefaults([
                'action_token_id' => $actionToken->action_token_id,
            ]);
        }
    }
    
}