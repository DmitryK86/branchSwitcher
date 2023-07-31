<?php

declare(strict_types=1);

namespace app\components\creators\config;

class ConfigCreatorFactory
{
    private array $creators = [
        AmsConfigForCasinoCreator::class,
        GPConfigForCasinoCreator::class,
        GPConfigForAmsCreator::class,
    ];

    /** @return ServiceConfigCreatorInterface[] */
    public function getCreators(string $fromType, string $toType): array
    {
        $creators = [];
        foreach ($this->creators as $creatorClassName) {
            if ($creatorClassName::fromEnvType() != $fromType) {
                continue;
            }
            if ($creatorClassName::forEnvType() != $toType) {
                continue;
            }

            $creators[] = new $creatorClassName();
        }

        return $creators;
    }
}
