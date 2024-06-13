<?php

declare(strict_types=1);

namespace app\managers;

use app\components\creators\config\ServiceConfigCreatorInterface;
use app\helpers\TransactionHelper;
use app\models\UserForeignEnv;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use yii\log\Logger;

class ForeignEnvsManager
{
    private const DEFAULT_LIMIT = 500;

    private string $url;
    private string $token;
    private int $limit;

    private array $mapping = [
        'ams-stage-' => [
            'type' => ServiceConfigCreatorInterface::TYPE_AMS,
            'domainTemplate' => 'ams.{NAME}.ams.infng.net'
        ],
    ];

    public function __construct()
    {
        $this->url = \Yii::$app->params['foreignEnvs']['url'];
        $this->token = \Yii::$app->params['foreignEnvs']['token'];
        $this->limit = (int)\Yii::$app->params['foreignEnvs']['limit'] ?? self::DEFAULT_LIMIT;
    }

    public function getAvailableServices(int $userId): array
    {
        $client = new Client();
        try {
            $response = $client->get("{$this->url}?limit={$this->limit}", [
                RequestOptions::HEADERS => [
                    'Authorization' => "Bearer {$this->token}",
                    'Accept' => 'application/json',
                ],
                RequestOptions::VERIFY => false,
            ]);
        } catch (\Throwable $e) {
            \Yii::getLogger()->log("Failed to get foreign envs info. Details: {$e->getMessage()}", Logger::LEVEL_ERROR);
            return [];
        }

        $response = json_decode($response->getBody()->getContents(), true);
        if (!$response) {
            return [];
        }

        $items = $response['items'] ?? null;
        if (!$items) {
            return [];
        }

        $availableItems = [];
        $existedItems = \Yii::$app->db->createCommand("SELECT name FROM user_foreign_envs WHERE user_id <> :uId", [':uId' => $userId])->queryColumn();
        foreach ($items as $item) {
            $itemName = $item['metadata']['name'] ?? '';
            if (in_array($itemName, $existedItems)) {
                continue;
            }
            foreach ($this->mapping as $itemNeedle => $data) {
                if (strpos($itemName, $itemNeedle) !== 0) {
                    continue;
                }

                $name = str_replace($itemNeedle, '', $itemName);
                $domain = str_replace('{NAME}', $name, $data['domainTemplate']);
                $availableItems[$itemName] = [
                    'type' => $data['type'],
                    'domain' => $domain,
                ];
            }
        }

        return $availableItems;
    }

    public static function getAvailableServicesForRelate(int $userId): array
    {
        $self = new self();
        $result = [];
        foreach ($self->getAvailableServices($userId) as $name => $data) {
            $result[$name] = $data['domain'];
        }

        return $result;
    }

    public function createForeignEnv(int $userId, array $itemNames): array
    {
        $availableItems = $this->getAvailableServices($userId);
        return TransactionHelper::transactional(function () use ($availableItems, $userId, $itemNames) {
            $result = [];
            foreach ($itemNames as $itemName) {
                if (!array_key_exists($itemName, $availableItems)) {
                    throw new \Exception("Foreign service with name '{$itemName}' doesnt exists");
                }
                $foreignEnv = new UserForeignEnv();
                $foreignEnv->user_id = $userId;
                $foreignEnv->name = $itemName;
                $foreignEnv->params = $availableItems[$itemName];

                if (!$foreignEnv->save()) {
                    throw new \Exception("Failed to save foreign service. Details: " . print_r($foreignEnv->getErrorSummary(true), true));
                }

                $result[] = $foreignEnv->id;
            }

            return $result;
        });
    }
}
