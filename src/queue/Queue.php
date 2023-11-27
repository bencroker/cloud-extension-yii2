<?php

namespace craft\cloud\queue;

use Craft;

class Queue extends \craft\queue\Queue
{
    public function executeJob(?string $id = null): bool
    {
        Craft::$app->onAfterRequest(function() use ($id) {
            $this->afterTransactions(fn() => parent::executeJob($id));
        });

        return true;
    }

    protected function afterTransactions(callable $callback, int $timeoutSeconds = 1): mixed
    {
        if (Craft::$app->getDb()->getTransaction() !== null) {
            sleep($timeoutSeconds);
            return $this->afterTransactions($callback, $timeoutSeconds);
        }

        return $callback();
    }
}
