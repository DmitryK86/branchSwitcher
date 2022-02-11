<?php
/**
 * Class Switcher
 */

class Switcher {
    const STATUS_OK = 'ok';
    const STATUS_ERROR = 'error';
    const REMOTES = 'remotes/origin/';
    const BACK_SUFFIX  ='-backend';

    protected $admin;
    protected $sudoUser;
    protected $projects;
    protected $logger;

    public function init($adminId){
        $config = require ('config.php');
        if (!in_array($adminId, $config['admins']??[])){
            throw new Exception('Admin not defined');
        }
        $this->admin = $adminId;
        $this->sudoUser = $config['username'] ?? '';
        if (!$this->sudoUser){
            throw new Exception('Sudo user not defined');
        }
        $this->projects = $config['projects'] ?? [];
        $this->logger = new Logger();
    }

    public function getProjects()
    {
        $result = [];
        foreach ($this->projects as $project){
            $result[] = $project;
            $result[] = $project . '-backend';
        }

        $this->sendResponse(self::STATUS_OK, $result);
    }

    public function checkBranch($projectName){
        $message = $this->applyCommand($projectName, 'git fetch; git status');
        $message = implode("<br>", array_splice($message, 0, 2));
        $this->sendResponse(self::STATUS_OK, $message);
    }

    public function updateCurrent($projectName){
        $message = $this->applyCommand($projectName, ' git pull', true);
        $this->sendResponse(self::STATUS_OK, $message);
    }

    public function checkAvailable($projectName){
        $branches = $this->applyCommand($projectName, ' git branch -a');
        $branches = array_map(function ($item){
            return str_replace(self::REMOTES, '', $item);
        }, $branches);

        $this->sendResponse(self::STATUS_OK, array_values(array_unique($branches)));
    }

    public function deploy($projectName, $branchName){
        $code = 0;
        if (strpos($projectName, self::BACK_SUFFIX) !== false){
            $project = str_replace(self::BACK_SUFFIX, '', $projectName);
            $message = shell_exec("ssh root@157.90.211.146 ./update_backend.sh {$branchName} {$this->admin} {$project}");
        } else {
            $command = "ssh dev@157.90.211.146 /var/www/{$this->admin}/{$projectName}/update.sh {$branchName}";
            $this->logger->log("Command: {$command}");
            exec($command, $message, $code);
        }

        $message = implode('<br>', array_filter(explode("\n", $message)));
        $this->logger->log("Switched branch {$branchName} on {$projectName}. code: {$code}");
        $this->sendResponse(self::STATUS_OK, $message);
    }

    private function applyCommand($projectName, $command, $sudoNeed = false, $applyMigrations = false){
        $path = "/var/www/{$this->admin}/{$projectName}";
        if ($sudoNeed){
            $command = $this->getSudo() . $command;
        }

        $resChangeDir = str_replace("\n", '', shell_exec("cd {$path} && pwd"));
        if (!$resChangeDir || $resChangeDir != $path){
            throw new Exception("Path {$path} not exists");
        }

        $result = exec("cd {$path} && $command", $out, $code);
//trace($result, false);
//trace($code);
        if ($result === false){
            $message = "Не удалось выполнить команду git ({$command}, code: {$code})";
            $this->logger->log("Error! {$projectName} / Details: {$message}");
            $this->sendResponse(self::STATUS_ERROR, $message);
        }

        return $out;
    }

    private function applyMigrations($path) {
        shell_exec('cd ' . $path .'; php yiic migrate --interactive=0');
    }

    public function sendResponse($status, $data){
        echo json_encode(['status' => $status, 'data' => $data]);
        exit(0);
    }

    private function getSudo(){
        return 'sudo -u ' . $this->sudoUser;
    }
}

