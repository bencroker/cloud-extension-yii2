<?php

namespace craft\cloud\controllers;

use Craft;
use craft\cloud\fs\Fs;
use craft\controllers\AssetsControllerTrait;
use craft\elements\Asset;
use craft\elements\conditions\ElementCondition;
use craft\events\ReplaceAssetEvent;
use craft\fields\Assets as AssetsField;
use craft\helpers\Assets;
use craft\helpers\Db;
use craft\i18n\Formatter;
use craft\web\Controller;
use DateTime;
use yii\base\Exception;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class CloudController extends Controller
{
    use AssetsControllerTrait;

    protected array|bool|int $allowAnonymous = ['debug'];

    public function actionDebug(): Response
    {
        $success = phpinfo();
        return $success ? $this->asSuccess() :  $this->asFailure();
    }

    public function actionGetUploadUrl(): Response
    {
        $this->requireAcceptsJson();
        $this->requirePostRequest();
        $originalFilename = $this->request->getRequiredBodyParam('filename');
        $extension = pathinfo($originalFilename, PATHINFO_EXTENSION);
        $filename = sprintf('%s.%s', uniqid('upload', true), $extension);
        $fieldId = $this->request->getBodyParam('fieldId');
        $assetId = $this->request->getBodyParam('assetId');
        $folderId = $this->request->getBodyParam('folderId');

        if ($assetId && !$folderId) {
            $folderId = Craft::$app->getAssets()->getAssetById($assetId)->folderId;
        }

        if (!$folderId && !$fieldId) {
            throw new BadRequestHttpException('No target destination provided for uploading');
        }

        if (!$folderId) {
            /** @var AssetsField|null $field */
            $field = Craft::$app->getFields()->getFieldById($fieldId);
            $elementId = $this->request->getBodyParam('elementId');
            $siteId = $this->request->getBodyParam('siteId');
            $element = $elementId
                ? Craft::$app->getElements()->getElementById($elementId, null, $siteId)
                : null;

            $folderId = $field->resolveDynamicPathToFolderId($element);
        }

        if (!$folderId) {
            throw new BadRequestHttpException('The target destination provided for uploading is not valid');
        }

        $folder = Craft::$app->getAssets()->findFolder(['id' => $folderId]);

        if (!$folder) {
            throw new BadRequestHttpException('The target folder provided for uploading is not valid');
        }

        $pathInVolume = "{$folder->path}$filename";

        /** @var Fs $fs */
        $fs = $folder->getVolume()->getFs();

        // TODO: use setting for expiry
        // TODO: tagging isn't working
        $url = $fs->presignedUrl('PutObject', $pathInVolume, new DateTime('+20 minutes'));

        return $this->asJson([
            'url' => $url,
            'originalFilename' => $originalFilename,
            'filename' => $filename,
            'bucket' => $fs->getBucketName(),
            'key' => $fs->prefixPath($filename),
            'folderId' => $folder->id,
        ]);
    }

    public function actionCreateAsset(): Response
    {
        $this->requireAcceptsJson();

        $filename = $this->request->getRequiredBodyParam('filename');
        $originalFilename = $this->request->getRequiredBodyParam('originalFilename');
        $size = $this->request->getBodyParam('size');
        $width = $this->request->getBodyParam('width');
        $height = $this->request->getBodyParam('height');
        $elementsService = Craft::$app->getElements();
        $lastModifiedMs = (int) $this->request->getBodyParam('lastModified');
        $dateModified = $lastModifiedMs ? DateTime::createFromFormat('U', (int)($lastModifiedMs / 1000)) : new DateTime();

        if (!$filename) {
            throw new BadRequestHttpException('No file was uploaded');
        }

        $folderId = (int)$this->request->getBodyParam('folderId') ?: null;

        // TODO: do I need to account for fieldId, since we resolve it in get-url?
        $fieldId = (int)$this->request->getBodyParam('fieldId') ?: null;

        if (!$folderId && !$fieldId) {
            throw new BadRequestHttpException('No target destination provided for uploading');
        }

        $assets = Craft::$app->getAssets();
        $selectionCondition = null;
        $element = null;

        if ($fieldId) {
            /** @var AssetsField|null $field */
            $field = Craft::$app->getFields()->getFieldById((int)$fieldId);

            if (!$field instanceof AssetsField) {
                throw new BadRequestHttpException('The field provided is not an Assets field');
            }

            if ($elementId = $this->request->getBodyParam('elementId')) {
                $siteId = $this->request->getBodyParam('siteId') ?: null;
                $element = $elementsService->getElementById($elementId, null, $siteId);
            }

            $folderId = $field->resolveDynamicPathToFolderId($element);
            $selectionCondition = $field->getSelectionCondition();
            if ($selectionCondition instanceof ElementCondition) {
                $selectionCondition->referenceElement = $element;
            }
        }

        if (empty($folderId)) {
            throw new BadRequestHttpException('The target destination provided for uploading is not valid');
        }

        $folder = $assets->findFolder(['id' => $folderId]);

        if (!$folder) {
            throw new BadRequestHttpException('The target folder provided for uploading is not valid');
        }

        // Check the permissions to upload in the resolved folder.
        $this->requireVolumePermissionByFolder('saveAssets', $folder);

        $targetFilename = Assets::prepareAssetName($originalFilename);
        $asset = new Asset();
        $asset->setFilename($filename);
        $asset->folderId = $folder->id;
        $asset->folderPath = $folder->path;
        $asset->setVolumeId($folder->volumeId);
        $asset->uploaderId = Craft::$app->getUser()->getId();
        $asset->avoidFilenameConflicts = true;
        $asset->dateModified = $dateModified;
        $asset->size = $size;
        $asset->width = $width;
        $asset->height = $height;

        if (!$selectionCondition) {
            $asset->newFilename = $targetFilename;
        }

        if (isset($originalFilename)) {
            $asset->title = Assets::filename2Title(pathinfo($originalFilename, PATHINFO_FILENAME));
        }

        $saved = $elementsService->saveElement($asset);

        // In case of error, let user know about it.
        if (!$saved) {
            // TODO: delete stray file
            $errors = $asset->getFirstErrors();
            return $this->asFailure(implode("\n", $errors));
        }

        if ($selectionCondition) {
            if (!$selectionCondition->matchElement($asset)) {
                // delete and reject it
                $elementsService->deleteElement($asset, true);
                return $this->asFailure(Craft::t('app', '{filename} isn’t selectable for this field.', [
                    'filename' => $originalFilename,
                ]));
            }

            $asset->newFilename = $targetFilename;
            $asset->setScenario(Asset::SCENARIO_MOVE);

            if (!$elementsService->saveElement($asset)) {
                $errors = $asset->getFirstErrors();
                return $this->asJson([
                    'error' => $this->asFailure(implode("\n", $errors)),
                ]);
            }
        }

        if ($asset->conflictingFilename !== null) {
            $conflictingAsset = Asset::findOne(['folderId' => $folder->id, 'filename' => $asset->conflictingFilename]);

            return $this->asJson([
                'conflict' => Craft::t('app', 'A file with the name “{filename}” already exists.', ['filename' => $asset->conflictingFilename]),
                'assetId' => $asset->id,
                'filename' => $asset->conflictingFilename,
                'conflictingAssetId' => $conflictingAsset->id ?? null,
                'suggestedFilename' => $asset->suggestedFilename,
                'conflictingAssetUrl' => ($conflictingAsset && $conflictingAsset->getVolume()->getFs()->hasUrls) ? $conflictingAsset->getUrl() : null,
            ]);
        }

        return $this->asSuccess(data: [
            'filename' => $asset->getFilename(),
            'assetId' => $asset->id,
        ]);
    }

    public function actionReplaceFile(): Response
    {
        $this->requireAcceptsJson();

        $assetId = $this->request->getBodyParam('assetId');
        $sourceAssetId = $this->request->getBodyParam('sourceAssetId');
        $filename = $this->request->getBodyParam('filename');
        $originalFilename = $this->request->getBodyParam('originalFilename');
        $targetFilename = $originalFilename ? Assets::prepareAssetName($originalFilename) : null;
        $assets = Craft::$app->getAssets();

        // Must have at least one existing asset (source or target).
        // Must have either target asset or target filename.
        // Must have either uploaded file or source asset.
        if ((empty($assetId) && empty($sourceAssetId)) ||
            (empty($assetId) && empty($targetFilename))
        ) {
            throw new BadRequestHttpException('Incorrect combination of parameters.');
        }

        $sourceAsset = null;
        $assetToReplace = null;

        if ($assetId && !$assetToReplace = $assets->getAssetById($assetId)) {
            throw new NotFoundHttpException('Asset not found.');
        }

        if ($sourceAssetId && !$sourceAsset = $assets->getAssetById($sourceAssetId)) {
            throw new NotFoundHttpException('Asset not found.');
        }

        $this->requireVolumePermissionByAsset('replaceFiles', $assetToReplace ?: $sourceAsset);
        $this->requirePeerVolumePermissionByAsset('replacePeerFiles', $assetToReplace ?: $sourceAsset);

        // Handle the Element Action
        if ($assetToReplace !== null && $filename) {
            if (!$this->replaceAssetFile($assetToReplace, $filename, $targetFilename)) {
                throw new Exception('Unable to replace asset.');
            }
        } elseif ($sourceAsset !== null) {
            // Or replace using an existing Asset

            // See if we can find an Asset to replace.
            if ($assetToReplace === null) {
                // Make sure the extension didn't change
                if (pathinfo($targetFilename, PATHINFO_EXTENSION) !== $sourceAsset->getExtension()) {
                    throw new Exception($targetFilename . ' doesn\'t have the original file extension.');
                }

                /** @var Asset|null $assetToReplace */
                $assetToReplace = Asset::find()
                    ->select(['elements.id'])
                    ->folderId($sourceAsset->folderId)
                    ->filename(Db::escapeParam($targetFilename))
                    ->one();
            }

            // If we have an actual asset for which to replace the file, just do it.
            // e.g. triggered by selecting "replace it" in asset index modal
            if ($assetToReplace) {
                // TODO: do this without downloading local file if both Cloud FSs
                $assets->replaceAssetFile(
                    $assetToReplace,
                    $sourceAsset->getCopyOfFile(),
                    $assetToReplace->getFilename()
                );
                Craft::$app->getElements()->deleteElement($sourceAsset);
            } else {
                // TODO: when/how does this occur?
                // If all we have is the filename, then make sure that the destination is empty and go for it.
                $volume = $sourceAsset->getVolume();
                $volume->deleteFile(rtrim($sourceAsset->folderPath, '/') . '/' . $targetFilename);
                $sourceAsset->newFilename = $targetFilename;
                // Don't validate required custom fields
                Craft::$app->getElements()->saveElement($sourceAsset);
                $assetId = $sourceAsset->id;
            }
        }

        $resultingAsset = $assetToReplace ?: $sourceAsset;

        return $this->asSuccess(data: [
            'assetId' => $assetId,
            'filename' => $resultingAsset->getFilename(),
            'formattedSize' => $resultingAsset->getFormattedSize(0),
            'formattedSizeInBytes' => $resultingAsset->getFormattedSizeInBytes(false),
            'formattedDateUpdated' => Craft::$app->getFormatter()->asDatetime($resultingAsset->dateUpdated, Formatter::FORMAT_WIDTH_SHORT),
            'dimensions' => $resultingAsset->getDimensions(),
        ]);
    }

    public function replaceAssetFile(Asset $asset, string $filename, string $targetFilename): bool
    {
        $assets = Craft::$app->getAssets();

        if ($assets->hasEventHandlers($assets::EVENT_BEFORE_REPLACE_ASSET)) {
            $event = new ReplaceAssetEvent([
                'asset' => $asset,
                'replaceWith' => '',
                'filename' => '',
            ]);
            $assets->trigger($assets::EVENT_BEFORE_REPLACE_ASSET, $event);
            $targetFilename = $event->filename ?: $targetFilename;
        }

        $asset->uploaderId = Craft::$app->getUser()->getId();
        $asset->avoidFilenameConflicts = true;
        $asset->setFilename($filename);
        $asset->newFilename = $targetFilename;

        $saved = Craft::$app->getElements()->saveElement($asset);

        if ($assets->hasEventHandlers($assets::EVENT_AFTER_REPLACE_ASSET)) {
            $assets->trigger($assets::EVENT_AFTER_REPLACE_ASSET, new ReplaceAssetEvent([
                'asset' => $asset,
                'filename' => $filename,
            ]));
        }

        return $saved;
    }
}
