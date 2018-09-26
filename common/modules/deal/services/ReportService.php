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
     */
    public function parseHtml($filePath)
    {
        $i = 0;
        $data = [];
        $eof = false;
        $dom = new Dom;
        $dom->loadFromFile($filePath);
        $balance = 0;
        $rows = $dom->find('tr');
        foreach ($rows as $row) {
            $firstCol = $row->firstChild();
            if (is_numeric($ticket = $firstCol->innerHtml)) {
                if (is_numeric($profit = $row->lastChild()->innerHtml)) {
                    $columns = $row->find('td');
                    if (($type = $columns[array_search('Type', $this->fields)]->innerHtml) == 'buy') {
                        foreach ($this->fields as $key => $col) {
                            if ($col == 'Profit') {
                                $balance += (int)$columns[$key]->innerHtml;
                                $data[$i][$col] = $balance;
                            } else {
                                $data[$i][$col] = $columns[$key]->innerHtml;
                            }
                        }
                    } else {
                        $balance += (int)$profit;
                        $data[$i] = [
                            'Ticket' => $ticket,
                            'Type' => $type,
                            'Profit' => $balance
                        ];
                    }
                    if (!$eof) {
                        $eof = true;
                    }
                    $i++;
                }
            } elseif ($eof) {
                break;
            }
        }
        FileHelper::deleteFile($filePath);
        return $data;
    }
}