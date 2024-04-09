<?php

declare(strict_types=1);

namespace app\managers;

use app\models\UserEnvironments;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use yii\log\Logger;

class AutotestRunner
{
    public function run(UserEnvironments $env): void
    {
        $params = \Yii::$app->params['autotest'];
        $url = $params['url'];

        $client = new Client();

        $errorMessage = null;
        try {
            $response = $client->post($url, [
                RequestOptions::AUTH => [
                    $params['login'],
                    $params['password']
                ],
                RequestOptions::FORM_PARAMS => [
                    'CONTAINER' => $env->environment_code,
                    'PROJECT' => $env->project->code,
                    'USER' => $env->user->username,
                ],
            ]);
        } catch (RequestException $e) {
            $response = $e->getResponse();
            $details = $response ? $response->getBody()->getContents() : 'unknown';
            $errorMessage = "Failed to run autotest. Details: {$details}";
        } catch (\Throwable $e) {
            $errorMessage = "Failed to run autotest. Details: {$e->getMessage()}";
        }

        $logger = \Yii::getLogger();
        if ($errorMessage) {
            $logger->log($errorMessage, Logger::LEVEL_ERROR, 'autotest.run');
            return;
        }

        $statusCode = isset($response) ? $response->getStatusCode() : 0;
        $logger->log("Autotest is running. Response status: {$statusCode}", Logger::LEVEL_WARNING, 'autotest.run');
    }
}
