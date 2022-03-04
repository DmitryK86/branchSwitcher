<?php

namespace app\managers;

use app\dto\SwitchLogDto;
use app\models\User;

/**
 * Class Switcher
 */
class Switcher
{
    const STATUS_OK = 'ok';
    const STATUS_ERROR = 'error';
    const REMOTES = 'remotes/origin/';
    const BACK_SUFFIX = '-backend';

    protected string $stageId;
    protected Logger $logger;
    protected ?string $projectName;
    protected ?string $branch;
    protected User $user;

    public function __construct(User $user, $projectName = null, $branch = null)
    {
        $this->user = $user;
        $this->stageId = $user->alias;
        $this->logger = new Logger();
        $this->projectName = str_replace(' ', '', $projectName);
        $this->branch = str_replace(' ', '', $branch);
    }

    public static function getProjects()
    {
        $result = [];
        foreach (\Yii::$app->params['projects'] ?? [] as $project) {
            $result[] = $project;
            $result[] = $project . self::BACK_SUFFIX;
        }

        return $result;
    }

    public function checkBranch()
    {
        $currentBranchData = $this->getCurrentBranchData();
        $message = implode("<br>", array_splice($currentBranchData, 0, 2));
        $this->sendResponse(self::STATUS_OK, $message);
    }

    public function updateCurrent()
    {
        $message = $this->applyCommand(' git status');
        $this->branch = str_replace('On branch ', '', $message[0]??'');
        if (!$this->branch){
            $this->sendResponse(self::STATUS_ERROR, "Ошибка при определении текущей ветки");
        }

        $this->deploy();
    }

    public function checkAvailable()
    {
        $branches = $this->applyCommand(' git branch -a');
        $branches = array_map(function ($item) {
            return str_replace([self::REMOTES, ' ', '*'], '', $item);
        }, $branches);

        $this->sendResponse(self::STATUS_OK, array_values(array_unique($branches)));
    }

    public function deploy()
    {
        $ip = \Yii::$app->params['ip'] ?? '';
        if (!$ip){
            throw new \Exception("IP not defined for back-deploy");
        }
//        if (strpos($this->projectName, self::BACK_SUFFIX) !== false) {
//            $project = str_replace(self::BACK_SUFFIX, '', $this->projectName);
//            $command = "ssh root@{$ip} ./update_backend.sh {$this->branch} {$this->stageId} {$project}";
//            $message = shell_exec($command);
//        } else {
//            $command = "ssh dev@{$ip} /var/www/{$this->stageId}/{$this->projectName}/update.sh {$this->branch}";
//            $message = shell_exec($command);
//        }
//
//        if (!is_array($message)){
//            $message = implode('<br>', array_filter(explode("\n", $message)));
//        }

        $dto = new SwitchLogDto();
        $dto->user = $this->user;
        $dto->alias = $this->projectName;
        $dto->to = $this->branch;
        $dto->from = str_replace('On branch ', '', $this->getCurrentBranchData()[0] ?? '');
        $this->logger->logSwitch($dto);

        $this->sendResponse(self::STATUS_OK, 'OK');
    }

    private function applyCommand($command)
    {
        $path = "/var/www/{$this->stageId}/{$this->projectName}";

        $resChangeDir = str_replace("\n", '', shell_exec("cd {$path} && pwd"));
        if (!$resChangeDir || $resChangeDir != $path) {
            throw new \Exception("Path {$path} not exists");
        }

        $result = exec("cd {$path} && $command", $out, $code);
        if ($result === false) {
            $message = "Не удалось выполнить команду git ({$command}, code: {$code})";
            $this->logger->logSwitch("Error! {$this->projectName} / Details: {$message}");
            $this->sendResponse(self::STATUS_ERROR, $message);
        }

        return $out;
    }

    public function sendResponse($status, $data)
    {
        echo json_encode(['status' => $status, 'data' => $data]);
        exit(0);
    }

    private function getCurrentBranchData(): array
    {
        return $this->applyCommand(' git fetch; git status');
    }
}

