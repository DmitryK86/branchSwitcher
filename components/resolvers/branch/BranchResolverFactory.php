<?php

declare(strict_types=1);

namespace app\components\resolvers\branch;

class BranchResolverFactory
{
    public function getByName(string $name): BranchResolverInterface
    {
        switch ($name) {
            case BitbucketResolver::NAME:
                return new BitbucketResolver();
            case GitlabResolver::NAME:
                return new GitlabResolver();
            default:
                throw new \Exception("Branch resolver {$name} not implemented");
        }
    }
}
