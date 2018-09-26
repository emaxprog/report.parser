<?php

namespace common\modules\deal\controllers;

use yii\web\Controller;

/**
 * Default controller for the `deal` module
 */
class DealController extends Controller
{
    public $enableCsrfValidation = false;

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'index' => __NAMESPACE__ . '\\deal\\IndexAction',
        ];
    }
}
