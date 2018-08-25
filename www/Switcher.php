<?php

class Switcher {
    const STATUS_OK = 'ok';
    const STATUS_ERROR = 'error';
    const REMOTES = 'remotes/origin/';

    protected $config = null;

    public function __construct(){
        $this->config = require ('config.php');
    }

    public function init(){
        $projects = array_keys($this->config['projects']);
        if (empty($projects) || in_array('name', $projects)) {
            $this->sendResponse(self::STATUS_ERROR, 'Check config. Invalid project name');
        }

        $this->sendResponse(self::STATUS_OK, $projects);
    }

    public function checkBranch($projectName){
        $message = explode(PHP_EOL, $this->applyCommand($projectName, 'git fetch; git status'));
        $message = implode("<br>", array_splice($message, 0, 2));
        $this->sendResponse(self::STATUS_OK, $message);
    }

    public function updateCurrent($projectName){
        $message = $this->applyCommand($projectName, ' git pull', true);
        $this->sendResponse(self::STATUS_OK, $message);
    }

    public function checkAvailable($projectName){
        $branches = explode(PHP_EOL, $this->applyCommand($projectName, ' git branch -a'));
        $branches = array_map(function ($item){
            return str_replace(self::REMOTES, '', $item);
        }, $branches);

        $this->sendResponse(self::STATUS_OK, array_values(array_unique($branches)));
    }

    public function checkoutBranch($projectName, $branchName){
        $message = $this->applyCommand($projectName, ' git checkout'. $branchName, true);
        $this->sendResponse(self::STATUS_OK, $message);
    }

    private function applyCommand($name, $command, $sudoNeed = false){
        $path = $this->config['projects'][$name]['path'];
        if ($sudoNeed){
            $command = $this->getSudo() . $command;
        }

        $result = shell_exec('cd ' . $path . '; ' . $command);
        if (!$result){
            $this->sendResponse(self::STATUS_ERROR, "Не удалось выполнить команду git ({$command})");
        }

        return $result;
    }

    private function applyMigrations() {

    }

    private function sendResponse($status, $data){
        echo json_encode(['status' => $status, 'data' => $data]);
        exit(0);
    }

    private function getSudo(){
        return 'sudo -u ' . $this->config['username'];
    }

}

