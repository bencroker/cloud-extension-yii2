<?php

namespace craft\cloud\queue;

use Craft;
use craft\helpers\Console;
use craft\queue\BaseJob;
use yii\console\Exception;

class TestJob extends BaseJob
{
    public string $message = '';
    public bool $throw = false;
    public int $seconds = 0;

    /**
     * @inheritdoc
     */
    protected function defaultDescription(): ?string
    {
        return Craft::t('app', 'Test Job');
    }

    /**
     * @inheritdoc
     */
    public function execute($queue): void
    {
        Console::stdout('Test job started.');

        if ($this->seconds) {
            Console::stdout("Sleeping for {$this->seconds} seconds…");
            sleep($this->seconds);
        }

        if ($this->throw) {
            Console::stdout('Throwing exception…');
            throw new Exception($this->message);
        }

        Console::stdout('Test job completed.');
    }
}
