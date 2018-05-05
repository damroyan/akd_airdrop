<?php
trait TraitParamRedirect {
    /**
     * @param $param
     * @param callable $callback
     * @return null
     */
    protected function getParamRedirect($param, $callback = null) {

        $redirect = null;
        if($param) {
            if(
                preg_match('@^/@ui', $param) &&
                !preg_match('@(:|/(go|api)/)@ui', $param)
            ) {
                $redirect = $param;
            }
        }

        if(!$redirect && $callback) {
            $redirect = $callback();
        }

        if(!$redirect) {
            $redirect = $this->url->get([
                'for' => 'frontend-v1-index-index'
            ]);
        }

        return $redirect;
    }
}