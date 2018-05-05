<?php
namespace Site\FrontendV1\Controllers;

use Phalcon\Mvc\Model\Transaction\Manager as TransactionManager;

class CompanyController extends Controller {

    public function initialize() {
        parent::initialize();

        $this->view->setVar('active_tab','company_');
    }


    /**
     * Наследствие базовой сборки скелетона при котором
     * один пользователь - одна компания
     *
     * @Role({"allow": ['user','admin']})
     * @return \Phalcon\Http\Response
     */
    public function myAction() {
        $company = \Model\Company::findFirst([
            'conditions'    => 'user_id = :user_id:',
            'bind'          => [
                'user_id'   => $this->user->user_id,
            ]
        ]);

        if (!$company) {
            $company = new \Model\Company();
            $company->assign([
                'user_id'       => $this->user->user_id,
                'company_name'  => __("%s Company",$this->user->user_name),
            ]);
            $company->save();
        }

        $this->view->setVar('company',$company);
    }
}