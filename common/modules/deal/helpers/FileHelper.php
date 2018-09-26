<?php
/**
 * Created by PhpStorm.
 * User: alexandr
 * Date: 25.09.18
 * Time: 17:12
 */

namespace common\modules\deal\helpers;

use Yii;
use yii\web\UploadedFile;

class FileHelper
{
    /**
     * Сохранение файла
     *
     * @param UploadedFile $uploadedFile
     * @return bool|string
     * @throws \yii\base\Exception
     */
    public static function saveFile(UploadedFile $uploadedFile)
    {
        $savePath = static::getSavePath(static::generateFileName($uploadedFile));
        return !$uploadedFile->saveAs($savePath) ?: $savePath;
    }

    /**
     * Удаление файла
     *
     * @param $filePath
     * @return bool
     */
    public static function deleteFile($filePath)
    {
        return unlink($filePath);
    }

    /**
     * Генерация уникального имени файла
     *
     * @param UploadedFile $uploadedFile
     * @return string
     * @throws \yii\base\Exception
     */
    protected static function generateFileName(UploadedFile $uploadedFile)
    {
        return Yii::$app->security->generateRandomString() . '.' . $uploadedFile->extension;
    }

    /**
     * Получение пути хранения файла отчета
     *
     * @param $fileName
     * @return string
     */
    protected static function getSavePath($fileName)
    {
        return Yii::getAlias('@frontend/web/uploads/') . $fileName;
    }
}