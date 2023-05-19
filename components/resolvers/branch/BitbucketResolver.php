<?php

declare(strict_types=1);

namespace app\components\resolvers\branch;

use app\exceptions\BranchResolveException;
use app\models\Repository;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use yii\log\Logger;

class BitbucketResolver implements BranchResolverInterface
{
    private const API_URL = 'https://api.bitbucket.org/2.0/repositories/netgamellcteam/{api_code}/refs/branches';

    public static function getName(): string
    {
        return 'bitbucket';
    }

    public function resolve(Repository $repository, string $searchBranchName): array
    {
        $url = str_replace('{api_code}', $repository->api_code, self::API_URL);

        $client = new Client();
        try {
            $response = $client->get($url . '?q=name ~ "' . $searchBranchName . '"', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => "Bearer {$repository->api_token}",
                ],
            ]);
        } catch (RequestException $e) {
            $response = $e->getResponse()->getBody()->getContents();
            \Yii::getLogger()->log("Failed to get branches. Details: {$response}", Logger::LEVEL_ERROR);

            throw new BranchResolveException();
        }

        $response = json_decode($response->getBody()->getContents(), true);

        return array_column($response['values'], 'name');
    }
}
