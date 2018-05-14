<?php
class ViewTask extends \Phalcon\CLI\Task {

    protected function directoryScan($dir, $ext) {
        $return = [];

        foreach (scandir($dir) as $file) {
            if (preg_match('@^\.@ui', $file)) {
                continue;
            }

            if (is_dir("{$dir}/{$file}")) {
                $return = array_merge(
                    $return,
                    $this->directoryScan("{$dir}/{$file}", $ext)
                );
                continue;
            }

            if (is_file("{$dir}/{$file}")) {
                if (preg_match("@\.{$ext}$@ui", $file)) {
                    $return[] = realpath("{$dir}/{$file}");
                    continue;
                }
            }

            continue;
        }

        return $return;
    }

    public function compileAction() {
        Console::WriteLine('Начало', Console::COLOR_LIGHT_GRAY);

        $directories = [
            APPLICATION_PATH . '/site/frontend-v1/views/',
            APPLICATION_PATH . '/site/api-v1/views/',
            APPLICATION_PATH . '/site/backend-v1/views/',
        ];
        
        foreach ($directories as $directory) {
            $compiler = new \Phalcon\Mvc\View\Engine\Volt\Compiler();
            add2Compiler($compiler);

            Console::WriteLine("Обработка папки: {$directory}", Console::COLOR_BLUE);

            $files = $this->directoryScan($directory, 'volt');
            Console::WriteLine("Найдено файлов: " . count($files), Console::COLOR_BLUE);

            foreach ($files as $file) {
                Console::WriteLine("Копилируем файл: {$file} -> " . md5($file), Console::COLOR_GREEN);

                $content = file_get_contents($file);
                $content = preg_replace('@\{\%\s+extends\s+"([^"]+)"\s+\%\}@ui', '{% extends "' . $directory . '/$1" %}', $content);

                file_put_contents(ROOT_PATH . '/cache/compiler/' . md5($file) . '.php', $compiler->compileString($content));
            }
        }


        $js_files = [
            __DIR__.'/../../public/front/frontend-v1/app.js',
            __DIR__.'/../../public/js/app/frontend-v1.js',
            __DIR__.'/../../public/js/app/frontend-v1/',
            __DIR__.'/../../public/front/tmpl/'
        ];

        $t_js = new Gettext\Translations();
        foreach($js_files as $file) {
            if (is_file($file)) {
                \Gettext\Extractors\JsCode::fromFile($file,$t_js);
            }

            if (is_dir($file)) {
                $dh1 = opendir($file);

                if (!$dh1 ) continue;

                while (($file1 = readdir($dh1)) !== false) {
                    if ($file1 == '.' || $file1 == '..' || $file == '.DS_Store') continue;

                    $f_src = $file.'/'.$file1;

                    if (!is_file($f_src)) continue;

                    Console::WriteLine('Start reading: ' .$f_src . "\n", Console::COLOR_GREEN);


                    Gettext\Extractors\JsCode::fromFile($f_src,$t_js);

                }

            }
        }
        $array = json_decode($t_js->toJsonString(),true);

        if (count($array['messages'][''])) {
            $res = [];
            print_r($array['messages']['']);

            foreach ($array['messages'][''] as $k => $v) {
                $res[] = "_('".$k."')";
            }

            file_put_contents(ROOT_PATH . '/cache/compiler/' . md5('JS_strings') . '.php',
                "<?php ".implode(';',$res)."?>"
            );
        }


        Console::WriteLine('Конец', Console::COLOR_LIGHT_GRAY);
    }

}