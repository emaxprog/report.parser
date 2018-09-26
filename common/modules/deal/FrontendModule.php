<?php

namespace common\modules\deal;

/**
 * deal module definition class
 */
class FrontendModule extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        $this->controllerMap = [
            'deal' => 'common\\modules\\deal\\controllers\\DealController',
        ];
    }
}
