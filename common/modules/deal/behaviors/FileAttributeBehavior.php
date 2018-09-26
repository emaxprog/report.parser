<?php
/**
 * Created by PhpStorm.
 * User: alexandr
 * Date: 25.09.18
 * Time: 17:45
 */

namespace common\modules\deal\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;

class FileAttributeBehavior extends Behavior
{
    public $fileProp;

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_VALIDATE => 'beforeValidate',
        ];
    }


    public function beforeValidate()
    {
        $this->owner->{$this->fileProp} = UploadedFile::getInstance($this->owner, $this->fileProp);
        return true;
    }
}