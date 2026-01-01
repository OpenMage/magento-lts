<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Reports
 */

/**
 *  Totals Class
 *
 * @package    Mage_Reports
 */
class Mage_Reports_Model_Totals
{
    /**
     * Retrieve count totals
     *
     * @param  Mage_Adminhtml_Block_Report_Product_Grid $grid
     * @param  string                                   $from
     * @param  string                                   $to
     * @return Varien_Object
     */
    public function countTotals($grid, $from, $to)
    {
        $columns = [];
        foreach ($grid->getColumns() as $col) {
            if ($col->getTotal() === null) {
                continue;
            }

            $columns[$col->getIndex()] = ['total' => $col->getTotal(), 'value' => 0];
        }

        $count = 0;
        $report = $grid->getCollection()->getReportFull($from, $to);
        foreach ($report as $item) {
            if ($grid->getSubReportSize() && $count >= $grid->getSubReportSize()) {
                continue;
            }

            $data = $item->getData();

            foreach (array_keys($columns) as $field) {
                if ($field !== '') {
                    $columns[$field]['value'] += $data[$field] ?? 0;
                }
            }

            $count++;
        }

        $data = [];
        foreach ($columns as $field => $arr) {
            if ($arr['total'] == 'avg') {
                if ($field !== '') {
                    if ($count != 0) {
                        $data[$field] = $arr['value'] / $count;
                    } else {
                        $data[$field] = 0;
                    }
                }
            } elseif ($arr['total'] == 'sum') {
                if ($field !== '') {
                    $data[$field] = $arr['value'];
                }
            } elseif (str_contains($arr['total'], '/')) {
                if ($field !== '') {
                    $data[$field] = 0;
                }
            }
        }

        $totals = new Varien_Object();
        $totals->setData($data);

        return $totals;
    }
}
