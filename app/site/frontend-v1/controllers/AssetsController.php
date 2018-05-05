<?php
namespace Site\FrontendV1\Controllers;

use \Phalcon\Assets\Manager;

class AssetsController extends Controller {
    private $_assets = null;

    public function initialize() {
        parent::initialize();
        $this->_assets = [
            'js' => [
                'backend' => [
                ],
            ],
            'css' => [
                'backend' => [
                    'front/backend-v1/default.css',
                ],
                'frontend' => [
                    'front/frontend-v1/default.css',
                ]
            ],
            'tmpl' => [
                'backend' => [

                ],
                'frontend' => [

                ]
            ],
        ];
    }

    /**
     * Assets TMPL
     *
     * @Role({"allow": ['public']})
     */
    public function tmplAction() {
        $params = $this->dispatcher->getParams();
        $assets = $params['assets'];

        $return = [];

        $d = dir(ROOT_PATH . "{$this->config->application->jsTemplateDir}");

        while (false !== ($entry = $d->read())) {
            if($entry == '.' || $entry == '..') {
                continue;
            }

            if(preg_match('@^_@', $assets)) {
                continue;
            }

            if($assets && preg_match('@^[a-z0-9\-]+$@ui', $assets)) {
                if(!preg_match("@^{$assets}@ui", $entry)) {
                    continue;
                }
            }

            $path = $d->path . $entry;

            if(preg_match('@^(.*)\.tmpl$@ui', $entry, $match) && is_file($path)) {
                $tmpl = 'template';

                $explode = explode('.', $match[1]);
                for($i = 0; $i < count($explode); $i++) {
                    $tmpl .= ucfirst($explode[$i]);
                }

                $content = file_get_contents($path);
                $content = preg_replace('!\s+!sumi', ' ', $content);
                $content = trim($content);

                $return[$tmpl] = $content;

            }
        }

        $response = new \Phalcon\Http\Response();
        $response->setContentType('text/javascript', 'utf-8');
        $response->setHeader('Pragma', 'cache');
        $response->setHeader('Cache-Control', 'public, max-age=' . (30 * 24 * 60 * 60));
        $response->setExpires(new \Datetime('+1 Month'));

        $response->setContent("define(['jquery', 'jqueryTmpl'], function(){console.log('[templates]', 'Init');return " . json_encode($return, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . ";});");

        return $response;
    }

    /**
     * Assets JS
     *
     * @Role({"allow": ['public']})
     */
    public function jsAction() {
        $params = $this->dispatcher->getParams();
        $assets = $params['assets'];

        if(!is_array($this->_assets['js'][$assets])) {
            return $this->error404();
        }

        $manager = new Manager();

        $collection = $manager->collection($assets);
        $collection->addFilter(new \Phalcon\Assets\Filters\Jsmin());

        foreach($this->_assets['js'][$assets] as $file) {
            $collection->addInlineJs(file_get_contents("{$_SERVER['DOCUMENT_ROOT']}/{$file}"));
        }

        $manager->useImplicitOutput(false);

        $response = new \Phalcon\Http\Response();
        $response->setContentType('text/javascript', 'utf-8');
        $response->setHeader('Pragma', 'cache');
        $response->setHeader('Cache-Control', 'public, max-age=' . (30 * 24 * 60 * 60));
        $response->setExpires(new \Datetime('+1 Month'));

        $explode = explode("\n", trim($manager->outputInlineJs($assets)));
        $explode[0] = preg_replace('@^<[^>]+>@iu', '', $explode[0]);
        $explode[count($explode) - 1] = preg_replace('@<[^>]+>$@iu', '', $explode[count($explode) - 1]);

        $response->setContent(
            trim(implode("\n", $explode))
        );

        return $response;
    }

    private function _addCss($file, $collection) {
        $content = file_get_contents($file);

        $filedocumentpath =  str_replace($_SERVER['DOCUMENT_ROOT'], '', $file);
        preg_match('@(/(.*)?/)([a-z0-9\@\-_\.]+\.([a-z]{3}))$@ui', $filedocumentpath, $match);

        $filepath   = $match[1];

        preg_match_all("@\@([a-z]+) \"([a-z0-9\@\-\.\/]+)\";(\n|\r)(\n|\r)?@ui", $content, $matches);
        $content = preg_replace("@\@([a-z]+) \"([a-z0-9\-\.\/]+)\";(\n|\r)(\n|\r)?@ui", "", $content);

        $content = preg_replace('@url\(("|\')?([a-z\-_\.0-9]+([a-z\-_\.0-9\@\/#\?]+)?)("|\')?\)@ui', "url({$filepath}$2)", $content);
        $content = preg_replace('@url\(("|\')?\.([^("|\'|\))]+)("|\')?\)@ui', "url({$filepath}.$2)", $content);

        $content = preg_replace('@[\/]+@sumi', '/', $content);

        $queryString = explode('?', $_SERVER['REQUEST_URI'], 2)[1];
        $content = preg_replace('@(\.(jpg|png|gif|svg))\)@ui', "$1?{$queryString})", $content);

        if (is_array($matches[1])) {
            foreach($matches[1] as $key => $value) {
                if ($value == 'import') {
                    $directory = dirname($file);

                    if(!preg_match('@^\.|/@', $matches[2][$key])) {
                        $this->_addCss(realpath($directory . '/' . $matches[2][$key]), $collection);
                    }
                    elseif (preg_match('@^\.@', $matches[2][$key])) {
                        $this->_addCss(realpath($directory . '/' . $matches[2][$key]), $collection);
                    }
                    else {
                        $this->_addCss($_SERVER['DOCUMENT_ROOT'] . $matches[2][$key], $collection);
                    }
              }
          }
        }

        $collection->addInlineCss($content, $collection);
    }

    /**
     * Assets CSS
     *
     * @Role({"allow": ['public']})
     */
    public function cssAction() {
        $params = $this->dispatcher->getParams();
        $assets = $params['assets'];

        if(!is_array($this->_assets['css'][$assets])) {
            return $this->error404();
        }

        $manager = new Manager();

        $collection = $manager->collection($assets);
        $collection->addFilter(new \Phalcon\Assets\Filters\Cssmin());

        foreach($this->_assets['css'][$assets] as $file) {
            $this->_addCss("{$_SERVER['DOCUMENT_ROOT']}/{$file}", $collection);
        }

        $manager->useImplicitOutput(false);

        $response = new \Phalcon\Http\Response();
        $response->setContentType('text/css', 'utf-8');
        $response->setHeader('Pragma', 'cache');
        $response->setHeader('Cache-Control', 'public, max-age=' . (30 * 24 * 60 * 60));
        $response->setExpires(new \Datetime('+1 Month'));

        $explode = explode("\n", trim($manager->outputInlineCss($assets)));
        $explode[0] = preg_replace('@^<[^>]+>@iu', '', $explode[0]);
        $explode[count($explode) - 1] = preg_replace('@<[^>]+>$@iu', '', $explode[count($explode) - 1]);

        $response->setContent(
            trim(implode("\n", $explode))
        );

        return $response;
    }

    /**
     * Assets translate for JS
     *
     * @Role({"allow": ['public']})
     */
    public function translateAction() {
        $params = $this->dispatcher->getParams();

        $t_file = $this->view->getViewsDir().'languages/'.$this->translate->getLanguage().'/js-'.$params['assets'].'-'.$this->translate->getLanguage().'.po';
        if (file_exists($t_file)) {
            $t = \Gettext\Translations::fromPoFile($t_file);

        } else {
            $t = \Gettext\Translations::fromPoFile($this->view->getViewsDir().'languages/message.pot');
        }

        echo $t->toJedString();
        exit;
    }
}