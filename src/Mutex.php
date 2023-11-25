<?php

namespace craft\cloud;

use craft\mutex\MutexTrait;
use Momento\Auth\CredentialProvider;
use Momento\Cache\CacheClient;
use Momento\Config\Configurations\Laptop;
use yii\mutex\RetryAcquireTrait;

class Mutex extends \yii\mutex\Mutex
{
    use MutexTrait;
    use RetryAcquireTrait;

    protected int $defaultTtlSeconds = 24 * 60 * 60;
    public string $name;
    public CacheClient $client;

    public function __construct($config = [])
    {
        $config += [
            'client' => $this->createClient($this->defaultTtlSeconds),
        ];

        parent::__construct($config);
    }

    protected static function createClient(int $defaultTtlSeconds): CacheClient
    {
        $configuration = Laptop::latest();
        $authProvider = CredentialProvider::fromEnvironmentVariable('CRAFT_CLOUD_MUTEX_AUTH_TOKEN');

        return new CacheClient(
            $configuration,
            $authProvider,
            $defaultTtlSeconds,
        );
    }

    /**
     * @inheritDoc
     */
    protected function acquireLock($name, $timeout = 0): bool
    {
        return $this->retryAcquire($timeout, function() use ($name) {
            if ($this->client->keyExists($this->name, $name)->asSuccess()) {
                return false;
            }

            return (bool) $this->client->set(
                $this->name,
                $name,
                (string) time(),
            )->asSuccess();
        });
    }

    /**
     * @inheritDoc
     */
    protected function releaseLock($name): bool
    {
        return (bool) $this->client->delete(
            $this->name,
            $name,
        )->asSuccess();
    }
}
