<?php

declare(strict_types=1);

namespace app\components\resolvers\branch;

use app\exceptions\BranchResolveException;
use app\models\Repository;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use yii\log\Logger;

class GitlabResolver implements BranchResolverInterface
{
    private const API_URL = 'https://gitlab.ntgtech.net/api/v4/projects/{api_code}/repository/branches';

    public const NAME = 'gitlab';

    public static function getName(): string
    {
        return self::NAME;
    }

    public function resolve(Repository $repository, string $searchBranchName): array
    {
        $url = str_replace('{api_code}', $repository->api_code, self::API_URL);

        $client = new Client();
        try {
            $response = $client->get($url . "?regex={$searchBranchName}", [
                'headers' => [
                    'PRIVATE-TOKEN' => $repository->api_token,
                ],
            ]);
        } catch (RequestException $e) {
            $response = $e->getResponse()->getBody()->getContents();
            \Yii::getLogger()->log("Failed to get branches. Details: {$response}", Logger::LEVEL_ERROR);

            throw new BranchResolveException();
        }

        $response = json_decode($response->getBody()->getContents(), true);

        return array_column($response, 'name');
    }
}
