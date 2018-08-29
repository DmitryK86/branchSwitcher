<?php
/**
 * Class Logger
 */

class Logger {

    const FILE_PATH = __DIR__ . '/../logs/activity.log';
    const NOT_ASSIGNED = 'n/a';


    public function log($message){
        $_mes = $this->createLogMessage($message);
        file_put_contents(self::FILE_PATH, $_mes, FILE_APPEND);
    }

    protected function createLogMessage($message){
        return "[{$this->getUserName()}][{$this->getTime()}] make next:" . PHP_EOL . $message . PHP_EOL;
    }

    protected function getUserName(){
        return $_COOKIE['qa_id'] ?? self::NOT_ASSIGNED;
    }

    protected function getTime(){
        return date('H:i:s d-m-Y');
    }
}