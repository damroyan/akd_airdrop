<?php
trait TraitUserMember {
    protected function setUserAccessLog($userId, $transaction = null) {
        $userAccessLog = new \Model\UserAccessLog();
        if($transaction) {
            $userAccessLog->setTransaction($transaction);
        }

        $userAccessLog->assign([
            'user_access_log_ip'    => $this->filter->sanitize(IP, 'ip'),
            'user_access_log_ua'    => $_SERVER['HTTP_USER_AGENT'],
            'user_id'               => $userId,
        ]);

        if($userAccessLog->save() === false) {
            $transaction->rollback("can't insert row in table 'user_access_log'");
        }        
    }
    
    protected function setUserMember($userId, $transaction = null) {
        $memberId = md5(time() . microtime(true) . rand(1000, 9999));

        $userMember = new \Model\UserMember();
        if($transaction) {
            $userMember->setTransaction($transaction);
        }

        $userMember->assign(array(
            'user_member_id'    => $memberId,
            'user_id'           => $userId,
            'user_member_cdate' => new \Phalcon\Db\RawValue('CURRENT_TIMESTAMP'),
            'user_member_ip'    => $this->filter->sanitize(IP, 'ip'),
            'user_member_ua'    => $_SERVER['HTTP_USER_AGENT'],
        ));

        if($userMember->save() === false) {
            $transaction->rollback("can't insert row in table 'user_member'");
        }
        $this->addUserMemberCookie($memberId);
    }
    
    /**
     * Добавляем member cookie
     *
     * @param $value
     */
    protected function addUserMemberCookie($value) {
        $this->cookies->set(
            $this->config->session->member_cookie,
            $value,
            time() + $this->config->session->member_cookie_lifetime,
            '/',
            null,
            $this->config->session->domain,
            true
        );
        $this->response->sendCookies();
    }

    /**
     * Удаляем member cookie
     */
    protected function removeUserMemberCookie() {
        $this->cookies->set(
            $this->config->session->member_cookie,
            null,
            -1,
            '/',
            null,
            $this->config->session->domain,
            true
        );
        $this->response->sendCookies();
    }
}