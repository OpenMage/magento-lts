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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Magento
 * @package     Magento_Db
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Mage_Mview_Model_Command_Factory
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
}
