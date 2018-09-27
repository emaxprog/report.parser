<?php
/**
 * Created by PhpStorm.
 * User: alexandr
 * Date: 25.09.18
 * Time: 17:26
 */

namespace common\modules\deal\services;

use yii\base\Model;
use PHPHtmlParser\Dom;
use yii\base\InvalidConfigException;
use common\modules\deal\helpers\FileHelper;

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
     * @param Model $form
     * @return bool|string
     * @throws \yii\base\Exception
     */
    public function upload(Model $form)
    {
        return $form->validate() ? FileHelper::saveFile($form->reportFile) : false;
    }

    /**
     * @param Model $form
     * @return array|bool
     * @throws \yii\base\Exception
     */
    public function processReport(Model $form)
    {
        try {
            if ($filePath = $this->upload($form)) {
                return $this->parseHtml($filePath);
            }
        } catch (InvalidConfigException $e) {
            $form->addErrors(['reportFile' => $e->getMessage()]);
        }
        return false;
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
        $balance = $i = 0;
        $data = [];
        $invalidHtml = true;
        $dom = new Dom;
        $dom->loadFromFile($filePath);
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
                            $currentVal = $columns[$key]->innerHtml;
                            if ($col == 'Profit') {
                                $balance += (float)$currentVal;
                                $data[$i][$col] = round($balance, 2);
                            } else {
                                $data[$i][$col] = $currentVal;
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