<?php

declare(strict_types=1);

namespace app\components\creators\config;

class ConfigCreatorFactory
{
    public function getCreator(string $type): ServiceConfigCreatorInterface
    {
        switch ($type) {
            case AmsCreator::TYPE:
                return new AmsCreator();
            default:
                throw new \Exception("Config creator with type '{$type}' not found");
        }
    }
}
