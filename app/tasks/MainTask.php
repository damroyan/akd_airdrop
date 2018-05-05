<?php
class MainTask extends \Phalcon\CLI\Task {
    public function mainAction() {
        Console::WriteLine('<p>Дефолтная проверка работоспособности cli - Базовые настройки ок<p>', Console::COLOR_GREEN);
    }
}