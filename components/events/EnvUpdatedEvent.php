<?php

declare(strict_types=1);

namespace app\components\events;

class EnvUpdatedEvent extends EnvEvent
{
    public const NAME = 'envUpdatedEvent';
}
