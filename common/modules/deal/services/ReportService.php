<?php
/**
 * Created by PhpStorm.
 * User: alexandr
 * Date: 25.09.18
 * Time: 17:26
 */

namespace common\modules\deal\services;

use PHPHtmlParser\Dom;
use common\modules\deal\forms\ReportForm;
use common\modules\deal\helpers\FileHelper;
use yii\base\InvalidConfigException;

class ReportService
{
    private $fields = [
        'Ticket',
        'Open Time',
        'Type',
        'Size',
        'Item',
        'Price',
        'S / L',
        'T / P',
        'Close Time',
        'Price',
        'Commission',
        'Taxes',
        'Swap',
        'Profit'
    ];

    /**
     * Загрузка файла
     *
     * @param ReportForm $form
     * @return bool|string
     * @throws \yii\base\Exception
     */
    public function upload(ReportForm $form)
    {
        return $form->validate() ? FileHelper::saveFile($form->reportFile) : false;
    }

    /**
     * Парсинг HTML
     *
     * @param $filePath
     * @return array
     * @throws InvalidConfigException
     */
    public function parseHtml($filePath)
    {
        $i = 0;
        $data = [];
        $dom = new Dom;
        $dom->loadFromFile($filePath);
        $invalidHtml = true;
        $balance = 0;
        $rows = $dom->find('tr');
        foreach ($rows as $row) {
            $firstCol = $row->firstChild();
            if (is_numeric($ticket = $firstCol->innerHtml)) {
                if (is_numeric($profit = str_replace(' ', '', $row->lastChild()->innerHtml))) {
                    $columns = $row->find('td');
                    $type = $columns[array_search('Type', $this->fields)]->innerHtml;
                    if (count($columns) < count($this->fields) && $type !== 'balance') {
                        continue;
                    }
                    if ($type == 'balance') {
                        $balance += (float)$profit;
                        $data[$i] = [
                            'Ticket' => $ticket,
                            'Type' => $type,
                            'Profit' => round($balance, 2)
                        ];
                    } else {
                        foreach ($this->fields as $key => $col) {
                            if ($col == 'Profit') {
                                $balance += (float)$columns[$key]->innerHtml;
                                $data[$i][$col] = round($balance, 2);
                            } else {
                                $data[$i][$col] = $columns[$key]->innerHtml;
                            }
                        }
                    }
                    $i++;
                }
            } else {
                if (preg_match('/Ticket.+Open Time.+Close Time.+Profit/', $row->innerHtml)) {
                    $invalidHtml = false;
                }
            }
        }
        if ($invalidHtml) {
            throw new InvalidConfigException('Invalid Report File.');
        }
        FileHelper::deleteFile($filePath);
        return $data;
    }
}