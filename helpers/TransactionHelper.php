<?php

declare(strict_types=1);

namespace app\helpers;

use Yii;

class TransactionHelper
{
    /**
     * @param callable $callback
     * @return mixed
     * @throws \Throwable
     */
    public static function transactional(callable $callback)
    {
        if (!Yii::$app->db->getTransaction()) {
            $transaction = Yii::$app->db->beginTransaction();
        }

        try {
            $result = $callback();

            if (isset($transaction)) {
                $transaction->commit();
            }
        } catch (\Throwable $e) {
            if (isset($transaction)) {
                $transaction->rollback();
            }

            throw $e;
        }

        return $result;
    }
}
