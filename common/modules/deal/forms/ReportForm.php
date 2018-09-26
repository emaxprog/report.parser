<?php
/**
 * Created by PhpStorm.
 * User: alexandr
 * Date: 25.09.18
 * Time: 17:08
 */

namespace common\modules\deal\forms;

use yii\base\Model;
use yii\web\UploadedFile;
use common\modules\deal\behaviors\FileAttributeBehavior;

class ReportForm extends Model
{
    /**
     * @var UploadedFile
     */
    public $reportFile;

    public function rules()
    {
        return [
            [['reportFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'html'],
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => FileAttributeBehavior::className(),
                'fileProp' => 'reportFile'
            ]
        ];
    }
}