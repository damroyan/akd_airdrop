<?php
class emailTask extends \Phalcon\CLI\Task {
    public function testAction() {
        $config = $this->di->get('config');

        Console::WriteLine('Проверка отправки email', Console::COLOR_LIGHT_GRAY);

        $message = \Swift_Message::newInstance()
            ->setSubject('Test subject')
            ->setFrom('test@mywishboard.com')
            ->setTo('dmitry.amroyan@gmail.com')
            ->setBody('Test content', 'text/html')
        ;


        $transport = \Swift_SmtpTransport::newInstance();
        $transport->setHost($config->mail->host ? $config->mail->host : 'localhost');
        $transport->setPort($config->mail->port ? $config->mail->port : '25');
        if($config->mail->user) {
            $transport->setUsername($config->mail->user);

            if($config->mail->password) {
                $transport->setPassword($config->mail->password);
            }
        }

        $mailer = \Swift_Mailer::newInstance($transport);
        $status = $mailer->send($message);

        var_dump($status);

        Console::WriteLine('Все отправил', Console::COLOR_LIGHT_GRAY);
    }

    public function queueAction() {
        $config = $this->di->get('config');

        Console::WriteLine('Получение списка неотправленных сообщений', Console::COLOR_LIGHT_GRAY);

        $emails = \Model\Email::find(array(
            "email_status = :email_status:",
            'bind' => array(
                'email_status' => \Model\Email::STATUS_WAIT,
            ),
            'order' => 'email_priority ASC, email_id ASC',
            'limit' => 30,
        ));

        Console::WriteLine('Получено: ' . count($emails), Console::COLOR_LIGHT_CYAN);

        foreach($emails as $email) {
            Console::WriteLine("Отправка сообщения #{$email->email_id}", Console::COLOR_LIGHT_BLUE);

            $to = [];
            $encode = json_decode($email->email_to, true);

            if(is_string($encode[0]) && filter_var($encode[0], FILTER_VALIDATE_EMAIL)) {
                $to[$encode[0]] = $encode[1];
            }
            else {
                foreach($encode as $key => $value) {
                    if(is_string($value)) {
                        continue;
                    }
                    else {
                        $to[$value[0]] = $value[1] ? $value[1] : $value[0];
                    }
                }
            }

            if(!count($to)) {
                $email->assign(array(
                    'email_status'  => \Model\Email::STATUS_ERROR,
                    'email_mdate'   => new \Phalcon\Db\RawValue('CURRENT_TIMESTAMP'),
                ));
                $email->save();

                Console::WriteLine("[{$config->mail->type}] Ошибка отправки", Console::COLOR_RED);

                continue;
            }

            foreach($to as $key => $value) {
                $emailBlacklist = \Model\EmailBlacklist::findFirst([
                    '
                        email_blacklist_id = :email_blacklist_id:
                        AND
                        email_blacklist_status = :email_blacklist_status:
                        AND
                        (email_blacklist_ddate IS NULL OR email_blacklist_ddate > NOW())
                    ',
                    'bind' => [
                        'email_blacklist_id' => md5(\Model\EmailBlacklist::prepareEmail($key)),
                        'email_blacklist_status' => 1,
                    ],
                    'limit' => 1,
                ]);

                if($emailBlacklist) {
                    unset($to[$key]);

                    $emailBlacklist->email_blacklist_count++;
                    $emailBlacklist->save();

                    Console::WriteLine("-> {$key} в черном списке", Console::COLOR_RED);
                }
            }

            if(!count($to)) {
                $email->assign(array(
                    'email_status'  => \Model\Email::STATUS_BLACKLIST,
                    'email_mdate'   => new \Phalcon\Db\RawValue('CURRENT_TIMESTAMP'),
                ));
                $email->save();

                Console::WriteLine("Ошибка отправки: Email в черном списке", Console::COLOR_RED);

                continue;
            }

            $from = array();
            $encode = json_decode($email->email_from, true);
            $from[$encode[0]] = $encode[1] ? $encode[1] : $encode[0];

            $message = \Swift_Message::newInstance()
                ->setSubject($email->email_subject)
                ->setFrom($from)
                ->setTo($to)
                ->setBody($email->email_content, 'text/html')
            ;

            switch($config->mail->type) {
                case 'smtp':
                    $transport = \Swift_SmtpTransport::newInstance();
                    $transport->setHost($config->mail->host ? $config->mail->host : 'localhost');
                    $transport->setPort($config->mail->port ? $config->mail->port : '25');
                    if($config->mail->user) {
                        $transport->setUsername($config->mail->user);

                        if($config->mail->password) {
                            $transport->setPassword($config->mail->password);
                        }
                    }

                    break;

                case 'php':
                case 'mail':
                default:
                    $transport = \Swift_MailTransport::newInstance();
                    break;
            }

            $mailer = \Swift_Mailer::newInstance($transport);

            $failedRecipients = array();
            $status = $mailer->send($message, $failedRecipients);

            if($status) {
                $email->assign(array(
                    'email_status'  => \Model\Email::STATUS_SEND,
                    'email_mdate'   => new \Phalcon\Db\RawValue('CURRENT_TIMESTAMP'),
                ));
                $email->save();

                Console::WriteLine("[{$config->mail->type}] Отправил", Console::COLOR_LIGHT_GREEN);
            }
            else {
                $email->assign(array(
                    'email_status'  => \Model\Email::STATUS_ERROR,
                    'email_mdate'   => new \Phalcon\Db\RawValue('CURRENT_TIMESTAMP'),
                    'email_failed'  => json_encode($failedRecipients),
                ));
                $email->save();

                Console::WriteLine("[{$config->mail->type}] Ошибка отправки", Console::COLOR_RED);
            }
        }

        Console::WriteLine('Все отправил', Console::COLOR_LIGHT_GRAY);
    }

    public function importAction() {
        /*$row = 1;
        if (($handle = fopen(APPLICATION_PATH.'/tasks/tmp/emails.csv', "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $e = new \Model\EmailsWebAgencies();
                $e->email = $data[0];
                //Console::WriteLine($e->email);
                $e->save();
                unset($e);
            }
            fclose($handle);
        }*/
    }

    public function spamAction() {

        $route = $this->di->get('url');
        $view = $this->di->get('view');
        $notification_name = 'spam_web_agencies_catalog';

        $view->domain = 'www.shopolog.ru';
        $view->utm_string = 'utm_source=newsletter&utm_medium=email&utm_campaign=WebAgenciesCatalog';


        $spam_list = \Model\EmailsWebAgencies::find(array(
            'conditions'    => $notification_name.'_date IS NULL',
            'limit'         => 30,
        ));
        foreach($spam_list as $e) {
            $email = trim($e->email);

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                Console::WriteLine("Плохой емаил: {$email}", Console::COLOR_RED);
                continue;
            }

            $params = array(

            );

            try {
                \Helper\Email::send(
                    clone $view,
                    null,
                    array($email,),
                    //array('dmitry.amroyan@gmail.com',
                    //    'anton@fivebirds.ru'
                    //),
                    $notification_name,
                    $params,
                    'catalog'
                );

                $e->assign(array(
                    $notification_name.'_date' => new \Phalcon\Db\RawValue('CURRENT_TIMESTAMP')
                ));
                $e->save();
                // exit;
            }
            catch(Exception $e) {
                Console::Exception($e);
                exit;
            }

            Console::WriteLine("Отправлен емаил: {$email}", Console::COLOR_GREEN);
        }

    }
}