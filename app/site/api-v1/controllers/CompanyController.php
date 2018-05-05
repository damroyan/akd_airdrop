<?php
namespace Site\ApiV1\Controllers;


class CompanyController extends Controller
{
    public function initialize()
    {
        parent::initialize();
    }

    /**
     * Обновление данных компании
     *
     * @Role({"allow": ['user','admin']})
     */
    public function dataEditAction() {

        $params = $this->getParams();
        if(is_http_response($params)) { return $params; }

        $result = $this->validation(
            new \Site\ApiV1\Validators\CompanyEdit(),
            $params
        );
        if(is_http_response($result)) { return $result; }

        $company = \Model\Company::findFirst([
            'conditions'    => 'user_id = :user_id:',
            'bind'          => [
                'user_id'       => $this->user->user_id,
            ]
        ]);
        if (!$company) {
            return \Response::Error(404,
                __('Company was not found. Please contact support to fix this problem.'));
        }

        $company->assign([
            'company_name'          => $params['company_name'],
            'company_description'   => $params['company_description'],
            'company_mdate'         => $params['company_mdate'],
        ]);
        $company->save();


        return \Response::Ok([
            'message'   => __('Company General data saved successfully'),
        ]);
    }

    /**
     * Обновление лого компании
     *
     * @Role({"allow": ['user','admin']})
     * @return \Phalcon\Http\Response
     */
    public function pictureAction() {

        $params = $this->getParams();
        if(is_http_response($params)) { return $params; }

        if(!isset($params['company_logo'])) {
            return \Response::Error(
                400,
                __('Company logo was not saved because of technical reasons (we not receive image). Please contact support.'));
        }

        $company = \Model\Company::findFirst([
            'conditions'    => 'user_id = :user_id:',
            'bind'          => [
                'user_id'       => $this->user->user_id,
            ]
        ]);
        if (!$company) {
            return \Response::Error(404,
                __('Company was not found. Please contact support to fix this problem.'));
        }

        // проверка, что файл локальный
        // сделать епта :)

        $uploadSingle = $this->uploadSingleImage(
            $company,
            $params,
            'company_logo',
            $this->config->image->directory_upload
        );

        // todo
        print_r($uploadSingle);
        exit;


        $this->user->assign([
            'user_picture'  => $params['user_picture'],
        ]);
        $this->user->save();

        $image =  \Helper\Image::getInstance();

        return \Response::Ok([
            'user_picture'  => $image->image($company->company_logo,50, 50, 'mwh', 'png', true)->__toString(),
        ]);

    }
}