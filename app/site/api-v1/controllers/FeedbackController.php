<?php
namespace Site\ApiV1\Controllers;

use \Zendesk\API\HttpClient as ZendeskAPI;

class FeedbackController extends Controller {
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
     * Обрабатывает приход фидбека
     *
     * @Role({"allow": ['public']})
     * @return \Phalcon\Http\Response
     */
    public function sendAction() {
        $params = $this->getParams();
        if(is_http_response($params)) { return $params; }

        $result = $this->validation(
            new \Site\ApiV1\Validators\Feedback(),
            $params
        );
        if(is_http_response($result)) { return $result; }

        if (!\Model\Feedback::isTypeCorrect($params['feedback_type'])) {
            return \Response::Error(400,__('Non supported feedback type received. Please contact with support directly via email.'));
        }

        // обработка подключенного zendeck
        if (!isset($params['email_only']) && $this->config->zendeck->username!='') {

            $custom_fields = $params;

            $client = new ZendeskAPI(
                $this->config->zendeck->subdomain,
                $this->config->zendeck->username);

            $client->setAuth('basic',
                ['username' => $this->config->zendeck->username,
                    'token' => $this->config->zendeck->token]);


            $newTicket = $client->tickets()->create([
                'subject'  => "[{$params['feedback_type']}] from  [{$params['feedback_user_name']}]",
                'comment'  => [
                    'body' => $params['feedback_description'],
                ],
                'priority' => 'normal',
                'type'      => 'task',
                'custom_fields' => $custom_fields,

            ]);


            return \Response::Ok(array('redirect'=>'/'));
        }

        // если zendeck не подключен или принудительно отправка на мейл,
        // то простая отправка на мейл
        \Helper\Email::send(
            clone $this->view, //объект view фалькона
            0, // текущий ID пользователя или 0
            array( explode(',',$this->config->notifications->feedback) ), //массив адресов куда слать
            'feedback', //название шаблона + строки перевода для subject письма
            array(
                'feedback' => $params,
                'unsubscribe'   => false// массив параметров для шаблона
            ),
            array(
                'email' => $this->config->contacts->noreply_email,
                'name'  => $this->config->contacts->noreply_name,
            ), // email который будет указан как мейл отправителя и имя пользователя отправителя
            $priority = 100 //приоритет отправки, используется в случае большого количества мейлом, чтобы, например, сначала отправлялись мейлы с активацией аккаунта, потом новостные. Чем больше приоритет, тем быстрее отправка.
        );

        return \Response::Ok(array('redirect'=>'/'));

    }

}