<?php

declare(strict_types=1);

namespace app\models\aware;

trait ActiveRecordAware
{
    public function saveOrFail(bool $runValidation = true, $attributeNames = null): void
    {
        if (!$this->save($runValidation, $attributeNames)) {
            $modelName = get_class($this);
            throw new \Exception("Failed to save {$modelName}. Details: " . print_r($this->getErrorSummary(true), true));
        }
    }
}
