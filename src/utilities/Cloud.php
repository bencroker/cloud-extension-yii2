<?php

namespace craft\cloud\utilities;

use Craft;
use craft\cloud\Module;
use craft\base\Utility;

/**
 * Cloud utility
 */
class Cloud extends Utility
{
    public static function displayName(): string
    {
        return 'Craft Cloud';
    }

    static function id(): string
    {
        return 'cloud';
    }

    public static function iconPath(): ?string
    {
        return Module::getInstance()->getBasePath() . DIRECTORY_SEPARATOR . 'icons/cloud.svg';
        // return Craft::getAlias('@appicons/craftid.svg');
    }

    static function contentHtml(): string
    {
        return Craft::$app->getView()->renderTemplate('cloud/utility');
    }
}
