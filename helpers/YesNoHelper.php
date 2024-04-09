<?php

declare(strict_types=1);

namespace app\helpers;

class YesNoHelper
{
    public static function getFilter(): array
    {
        return [1 => 'Да', 0 => 'Нет'];
    }

    public static function getValue(bool $flag): string
    {
        return $flag ? '<span class="label label-success">Да</span>' : '<span class="label label-danger">Нет</span>';
    }
}
