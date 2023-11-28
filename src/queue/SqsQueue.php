<?php

namespace craft\cloud\queue;

use Craft;

class SqsQueue extends \yii\queue\sqs\Queue
{
    protected function pushMessage($message, $ttr, $delay, $priority): string
    {
        Craft::$app->onAfterRequest(fn() =>
            /**
             * https://github.com/yiisoft/yii2-queue/pull/502
             * @phpstan-ignore-next-line
             */
            parent::pushMessage($message, (string) $ttr, $delay, null)
        );

        return '';
    }
}
