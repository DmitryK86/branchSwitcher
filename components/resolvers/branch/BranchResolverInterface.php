<?php

namespace app\components\resolvers\branch;

use app\models\Repository;

interface BranchResolverInterface
{
    public static function getName(): string;
    public function resolve(Repository $repository, string $searchBranchName): array;
}
