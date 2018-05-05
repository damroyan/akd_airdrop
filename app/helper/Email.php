<?php
namespace Helper;

class Email {

    /**
     * @param $view \Phalcon\Mvc\View
     * @param $to
     * @param $template
     * @param array $params
     * @param array $from
     * @param int $priority
     * @param string $label
     */
    static public function send($view, $userId = null, $to, $template, $params = array(), $from = array(), $priority = 0, $label = null) {
        $instance = new self();

        $toEmail = array();
        if(is_string($to)) {
            $toEmail = [
                $to
            ];

        }
        elseif(is_array($to)) {
            $toEmail = array();
            foreach($to as $value) {
                if(is_array($value)) {
                    list($value1, $value2) = $value;

                    $value2 = trim($value2);
                    if(!$value2) {
                        $value2 = 'Unknow {to} username';
                    }

                    if(filter_var($value1, FILTER_VALIDATE_EMAIL)) {
                        $toEmail[] = array($value1, $value2);
                    }
                }
                elseif(filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $toEmail[] = array($value, );
                }
            }
        }

        if(!isset($from['email']) || !isset($from['name'])) {
            $fromEmail = 'invalid_from@yourdomain.com';
            $fromUsername = 'Mr. Invalid User';
        }
        else {
            $fromEmail = $from['email'];
            $fromUsername = $from['name'];

            if(!filter_var($fromEmail, FILTER_VALIDATE_EMAIL)) {
                $fromEmail = 'invalid_from@skeleton.ru';
            }

            $fromUsername = trim($fromUsername);
            if(!$fromUsername) {
                $fromUsername = 'Unknow {from} username';
            }
        }

        if (!$params['email_subject']) {
            $subject = "[{$template} email] Forget to define email_subject";
        } else {
            $subject = $params['email_subject'];
        }
        $content = self::getContent($view, $template, $params);

        $email = new \Model\Email();
        $email->assign(array(
            'email_to'      => json_encode($toEmail),
            'user_id'       => $userId > 0 ? $userId : null,
            'email_cdate'   => new \Phalcon\Db\RawValue('CURRENT_TIMESTAMP'),
            'email_status'  => \Model\Email::STATUS_WAIT,
            'email_from'    => json_encode(array($fromEmail, $fromUsername)),
            'email_subject' => $subject,
            'email_content' => $content,
            'email_priority'=> $priority,
            'email_label'   => $label,
        ));

        if($email->save()) {
            return array($subject, $content);
        }
        else {
            throw new \Exception("Can't add email '{$template}'' to queue");
        }
    }

    /**
     * @param $view \Phalcon\Mvc\View
     * @param $template
     * @param array $params
     */
    static function getContent($view, $template, $params){
        $dir = APPLICATION_PATH . "/site/frontend-v1/views/";
        if ($params['language']):
            if (file_exists($dir.'languages/'.$params['language'].'/email/'.$template.'.volt')){
                $dir = $dir.'languages/'.$params['language'].'/';
            };
        endif;

        $view->setViewsDir($dir);
        $view->start();
        $view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_ACTION_VIEW);
        $view->setVars($params, true);
        $view->render('email', $template);
        $view->finish();
        return $view->getContent();
    }
}