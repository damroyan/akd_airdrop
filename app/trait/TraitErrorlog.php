<?php
/**
 * @property \Phalcon\Filter $filter
 */
trait TraitErrorlog {
    private function _errorlog($code = null) {
        $date = date('Y-m-d H:i:s');
        $ip = $this->filter->sanitize(IP, 'ip');

        $get = $_GET;
        unset($get['_url']);

        $get = http_build_query($get);
        $post = http_build_query($_POST);

        error_log("[PHALCON] [date {$date}] [code {$code}] [client {$ip}] [host {$_SERVER['HTTP_HOST']}] [method {$_SERVER['REQUEST_METHOD']}] {$_SERVER['REQUEST_URI']} [query {$get}] [post {$post}] [referer {$_SERVER['HTTP_REFERER']}] [ua {$_SERVER['HTTP_USER_AGENT']}]");
    }
}