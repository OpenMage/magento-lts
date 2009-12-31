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
 * @category    Mage
 * @package     Mage_Index
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Index_Model_Mysql4_Setup extends Mage_Core_Model_Resource_Setup
{
    /**
     * Apply Index moduke DB updates and sync indexes declaration
     */
    public function applyUpdates()
    {
        parent::applyUpdates();
        $this->_syncIndexes();
    }

    /**
     * Sync indexes declarations in config and in DB
     */
    protected function _syncIndexes()
    {
        $indexes = Mage::getConfig()->getNode(Mage_Index_Model_Process::XML_PATH_INDEXER_DATA);
        $indexCodes = array();
        foreach ($indexes->children() as $code => $index) {
            $indexCodes[] = $code;
        }
        $table = $this->getTable('index/process');
        $connection = $this->getConnection();
        $existingIndexes = $connection->fetchCol('SELECT indexer_code FROM '.$table);
        $delete = array_diff($existingIndexes, $indexCodes);
        $insert = array_diff($indexCodes, $existingIndexes);

        if (!empty($delete)) {
            $connection->delete($table, $connection->quoteInto('indexer_code IN (?)', $delete));
        }
        if (!empty($insert)) {
            $connection->insertArray($table, array('indexer_code'), $insert);
        }
    }
}
