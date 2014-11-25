<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Magento
 * @package     Magento_Db
 * @copyright  Copyright (c) 2006-2014 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Magento PDO MySQL DB adapter
 *
 * @category    Magento
 * @package     Magento_Db
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Db_Adapter_Pdo_Mysql extends Varien_Db_Adapter_Pdo_Mysql
{
    /**
     * Returns flag is transaction now?
     *
     * @return bool
     */
    public function isTransaction()
    {
        return (bool)$this->_transactionLevel;
    }

    /**
     * Batched insert of specified select
     *
     * @param Varien_Db_Select $select
     * @param string $table
     * @param array $fields
     * @param bool $mode
     * @param int $step
     * @return int
     */
    public function insertBatchFromSelect(Varien_Db_Select $select, $table, array $fields = array(),
                                          $mode = false, $step = 10000
    ) {
        $limitOffset = 0;
        $totalAffectedRows = 0;

        do {
            $select->limit($step, $limitOffset);
            $result = $this->query(
                $this->insertFromSelect($select, $table, $fields, $mode)
            );

            $affectedRows = $result->rowCount();
            $totalAffectedRows += $affectedRows;
            $limitOffset += $step;
        } while ($affectedRows > 0);

        return $totalAffectedRows;
    }

    /**
     * Retrieve bunch of queries for specified select splitted by specified step
     *
     * @param Varien_Db_Select $select
     * @param string $entityIdField
     * @param int $step
     * @return array
     */
    public function splitSelect(Varien_Db_Select $select, $entityIdField = '*', $step = 10000)
    {
        $countSelect = clone $select;

        $countSelect->reset(Zend_Db_Select::COLUMNS);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->columns('COUNT(' . $entityIdField . ')');

        $row = $this->fetchRow($countSelect);
        $totalRows = array_shift($row);

        $bunches = array();
        for ($i = 0; $i <= $totalRows; $i += $step) {
            $bunchSelect = clone $select;
            $bunches[] = $bunchSelect->limit($step, $i);
        }

        return $bunches;
    }
}
