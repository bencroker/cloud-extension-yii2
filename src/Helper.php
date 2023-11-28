<?php

namespace craft\cloud;

use Craft;
use craft\cache\DbCache;
use craft\cloud\fs\BuildArtifactsFs;
use craft\cloud\Helper as CloudHelper;
use craft\cloud\queue\SqsQueue;
use craft\cloud\runtime\Runtime;
use craft\db\Table;
use craft\helpers\App;
use craft\helpers\ConfigHelper;
use craft\mutex\NullMutex;
use craft\queue\Queue as CraftQueue;
use HttpSignatures\Context;
use Illuminate\Support\Collection;
use yii\di\Instance;
use yii\mutex\Mutex as YiiMutex;
use yii\web\DbSession;

class Helper
{
    public static function isCraftCloud(): bool
    {
        return App::env('CRAFT_CLOUD') ?? App::env('AWS_LAMBDA_RUNTIME_API') ?? false;
    }

    public static function artifactUrl(string $path = ''): string
    {
        return (new BuildArtifactsFs())->createUrl($path);
    }

    public static function setMemoryLimit(int|string $limit, int|string $offset = 0): int|float
    {
        $memoryLimit = ConfigHelper::sizeInBytes($limit) - ConfigHelper::sizeInBytes($offset);
        Craft::$app->getConfig()->getGeneral()->phpMaxMemoryLimit((string) $memoryLimit);
        Craft::info("phpMaxMemoryLimit set to $memoryLimit");

        return $memoryLimit;
    }

    /**
     * A version of tableExists that doesn't rely on the cache component
     */
    public static function tableExists(string $table): bool
    {
        $sql = <<<SQL
SELECT count(*)
FROM [[information_schema]].tables
WHERE [[table_name]] = :tableName
AND [[table_schema]] = :tableSchema
SQL;
        $command = Craft::$app->getDb()->createCommand($sql, [
            ':tableName' => Craft::$app->getDb()->getSchema()->getRawTableName($table),
            ':tableSchema' => Craft::$app->getDb()->getIsMysql()
                ? Craft::$app->getConfig()->getDb()->database
                : Craft::$app->getConfig()->getDb()->schema,
        ]);

        return $command->queryScalar() > 0;
    }

    public static function modifyConfig(array &$config, string $appType): void
    {
        if (!CloudHelper::isCraftCloud()) {
            return;
        }

        if ($appType === 'web') {
            $config['components']['session'] = function() {
                $config = App::sessionConfig();

                if (static::tableExists(Table::PHPSESSIONS)) {
                    $config['class'] = DbSession::class;
                    $config['sessionTable'] = Table::PHPSESSIONS;
                }

                return Craft::createObject($config);
            };
        }

        $config['components']['cache'] = function() {
            $config = static::tableExists(Table::CACHE) ? [
                'class' => DbCache::class,
                'cacheTable' => Table::CACHE,
                'defaultDuration' => Craft::$app->getConfig()->getGeneral()->cacheDuration,
            ] : App::cacheConfig();

            return Craft::createObject($config);
        };

        $config['components']['mutex'] = function() use ($config) {
            $mutex = Module::getInstance()->getConfig()->useMutex
                ? Craft::createObject([
                    'class' => Mutex::class,
                    'namePrefix' => Module::getInstance()->getConfig()->environmentId . ':',
                    'name' => App::env('CRAFT_CLOUD_MUTEX_NAME'),
                ])
                : $config['components']['mutex'];

            return Instance::ensure($mutex, YiiMutex::class);
        };

        $config['components']['queue'] = function() {
            $ttr = Runtime::MAX_EXECUTION_SECONDS - 1;

            return Craft::createObject([
                'class' => CraftQueue::class,
                'ttr' => $ttr,
                'proxyQueue' => Module::getInstance()->getConfig()->useQueue ? [
                    'class' => SqsQueue::class,
                    'url' => Module::getInstance()->getConfig()->sqsUrl,
                    'region' => Module::getInstance()->getConfig()->getRegion(),
                    'ttr' => $ttr,
                ] : null,
            ]);
        };

        $config['components']['assetManager'] = function() {
            $config = [
                'class' => AssetManager::class,
            ] + App::assetManagerConfig();

            return Craft::createObject($config);
        };
    }

    public static function createSigningContext(iterable $headers = []): Context
    {
        $headers = Collection::make($headers);

        return new Context([
            'keys' => [
                'hmac' => Module::getInstance()->getConfig()->cdnSigningKey,
            ],
            'algorithm' => 'hmac-sha256',
            'headers' => $headers->push('host')->all(),
        ]);
    }
}
