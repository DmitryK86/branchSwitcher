<?php

declare(strict_types=1);

namespace app\components\resolvers\branch;

class BranchResolverFactory
{
    public function getByName(string $name): BranchResolverInterface
    {
        return new BitbucketResolver();
    }
}