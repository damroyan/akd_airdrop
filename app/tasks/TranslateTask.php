<?php
class TranslateTask extends \Phalcon\CLI\Task {

    /**
     * генерирует файлы .pot для переводов
     */
    public function generateAction() {
        $date = date('Y-m-d:H:00');

        $tmp_folder = __DIR__.'/../../tmp/';

        $php_dirs = [
            __DIR__.'/../site/frontend-v1/controllers/',
            __DIR__.'/../site/backend-v1/controllers/',
            __DIR__.'/../site/api-v1/controllers/',

            __DIR__.'/../site/frontend-v1/validators/',
            __DIR__.'/../site/backend-v1/validators/',
            __DIR__.'/../site/api-v1/validators/',

            __DIR__.'/../validators/',
        ];

        $volt_dirs = [
            __DIR__.'/../site/frontend-v1/views/',
            __DIR__.'/../site/backend-v1/views/',
        ];

        $js_files = [
            __DIR__.'/../../public/front/frontend-v1/app.js',
            __DIR__.'/../../public/front/backend-v1/app.js',
        ];


        $t_php = new Gettext\Translations();
        foreach($php_dirs as $dir) :
            if ($dh = opendir($dir)):
                while (($file = readdir($dh)) !== false) {
                    if ($file == '.' || $file =='..') continue;

                    Console::WriteLine('Start reading: '. $file."\n",Console::COLOR_GREEN);


                    Gettext\Extractors\PhpCode::fromFile($dir.$file,$t_php);
                    //$translations[] = $t;

                }
                closedir($dh);
            endif;
        endforeach;



        $t_volt = new Gettext\Translations();
        foreach($volt_dirs as $dir) :
            if ($dh = opendir($dir)):
                while (($file = readdir($dh)) !== false) {
                    if ($file == '.' || $file == '..' || $file == 'languages') continue;

                    $dh1 = opendir($dir.'/'.$file);
                    while (($file1 = readdir($dh1)) !== false) {
                        if ($file1 == '.' || $file1 == '..') continue;

                        Console::WriteLine('Start reading: ' . $file.'/'.$file1 . "\n", Console::COLOR_GREEN);


                        Gettext\Extractors\JsCode::fromFile($dir.'/'.$file.'/'.$file1,$t_volt);
                    }
                }
            endif;
        endforeach;
        $t_volt->mergeWith($t_php);
        $t_volt->toPoFile($tmp_folder.'translations-server-'.$date.'.pot');


        $t_js = new Gettext\Translations();
        foreach($js_files as $file) {
            \Gettext\Extractors\JsCode::fromFile($file,$t_js);
        }

        $t_js->toPoFile($tmp_folder.'translations-js-'.$date.'.pot');
    }


}