<?php

namespace craft\cloud\queue;

use Craft;
use samdark\log\PsrMessage;

class SqsQueue extends \yii\queue\sqs\Queue
{
    protected function pushMessage($message, $ttr, $delay, $priority): string
    {
        Craft::error(new PsrMessage('SQS request on pushMessage', [
            'QueueUrl' => $this->url,
            'MessageBody' => $message,
            'DelaySeconds' => $delay,
            'MessageAttributes' => [
                'TTR' => [
                    'DataType' => 'Number',
                    'StringValue' => $ttr,
                ],
            ],
        ]));

        /**
         * Delay pushing to SQS until after request is processed.
         *
         * Without this, a job might be pushed to SQS from within a DB
         * transaction and send back to Craft for handling before the
         * transaction ends. At this point, the job ID doesn't exist,
         * so the craft cloud/queue/exec command will fail and SQS
         * will consider the job processed. Once the transaction ends,
         * the job will exist and be indefinitely pending.
         */
        Craft::$app->onAfterRequest(function() use ($message, $ttr, $delay) {
            Craft::error(new PsrMessage('SQS request on onAfterRequest', [
                'QueueUrl' => $this->url,
                'MessageBody' => $message,
                'DelaySeconds' => $delay,
                'MessageAttributes' => [
                    'TTR' => [
                        'DataType' => 'Number',
                        'StringValue' => $ttr,
                    ],
                ],
            ]));

            /**
             * @phpstan-ignore-next-line
             * @see https://github.com/yiisoft/yii2-queue/pull/502
             *
             * Priority is not supported by SQS
             */
            return parent::pushMessage($message, (string) $ttr, $delay, null);
        });

        // Return anything but null, as we don't have an SQS message ID yet.
        return '';
    }
}
