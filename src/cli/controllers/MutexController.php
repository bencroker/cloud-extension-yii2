<?php

namespace craft\cloud\cli\controllers;

use Craft;
use craft\console\Controller;
use yii\console\ExitCode;

class MutexController extends Controller
{
    public function actionAcquire(string $name): int
    {
        return Craft::$app->getMutex()->acquire($name)
            ? ExitCode::OK
            : ExitCode::UNSPECIFIED_ERROR;
    }

    public function actionRelease(string $name): int
    {
        return Craft::$app->getMutex()->release($name)
            ? ExitCode::OK
            : ExitCode::UNSPECIFIED_ERROR;
    }
}
