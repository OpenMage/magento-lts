<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Reports
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 *  Totals Class
 *
 * @category   Mage
 * @package    Mage_Reports
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Reports_Model_Totals
{
    /**
     * Retrieve count totals
     *
     * @param Mage_Adminhtml_Block_Report_Grid $grid
     * @param string $from
     * @param string $to
     * @return Varien_Object
     */
    public function countTotals($grid, $from, $to)
    {
        $columns = [];
        foreach ($grid->getColumns() as $col) {
            if ($col->getTotal() === null) {
                continue;
            }
            $columns[$col->getIndex()] = ["total" => $col->getTotal(), "value" => 0];
        }

        $count = 0;
        $report = $grid->getCollection()->getReportFull($from, $to);
        foreach ($report as $item) {
            if ($grid->getSubReportSize() && $count >= $grid->getSubReportSize()) {
                continue;
            }
            $data = $item->getData();

            foreach ($columns as $field => $a) {
                if ($field !== '') {
                    $columns[$field]['value'] += $data[$field] ?? 0;
                }
            }
            $count++;
        }
        $data = [];
        foreach ($columns as $field => $a) {
            if ($a['total'] == 'avg') {
                if ($field !== '') {
                    if ($count != 0) {
                        $data[$field] = $a['value'] / $count;
                    } else {
                        $data[$field] = 0;
                    }
                }
            } elseif ($a['total'] == 'sum') {
                if ($field !== '') {
                    $data[$field] = $a['value'];
                }
            } elseif (strpos($a['total'], '/') !== false) {
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
