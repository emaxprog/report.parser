<?php
/**
 * Файл класса IndexAction
 *
 * @copyright Copyright (c) 2018, Oleg Chulakov Studio
 * @link http://chulakov.com/
 */

namespace common\modules\deal\controllers\deal;

use Yii;
use yii\base\Action;
use yii\web\Controller;
use common\modules\deal\forms\ReportForm;
use common\modules\deal\services\ReportService;

/**
 * Класс действие для получения списка советов
 */
class IndexAction extends Action
{
    protected $service;

    public function __construct($id, Controller $controller, array $config = [])
    {
        parent::__construct($id, $controller, $config);
        $this->service = new ReportService();
    }

    /**
     * Действие для парсинга отчета
     *
     * @return string|\yii\web\Response
     * @throws \yii\base\Exception
     */
    public function run()
    {
        $form = new ReportForm();

        if (Yii::$app->request->isPost) {
            if ($filePath = $this->service->upload($form)) {
                return $this->controller->asJson($this->service->parseHtml($filePath));
            }

            Yii::$app->response->statusCode = 403;
            return $this->controller->asJson([
                'errors' => $form->errors
            ]);
        }

        return $this->controller->render('index', ['form' => $form]);
    }
}
