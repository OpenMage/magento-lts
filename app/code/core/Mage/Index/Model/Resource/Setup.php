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
 * @package    Mage_Index
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Index Setup Model
 *
 * @category   Mage
 * @package    Mage_Index
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Index_Model_Resource_Setup extends Mage_Core_Model_Resource_Setup
{
    /**
     * Apply Index module DB updates and sync indexes declaration
     *
     * @return void
     */
    public function applyUpdates()
    {
        parent::applyUpdates();
        $this->_syncIndexes();
    }

    /**
     * Sync indexes declarations in config and in DB
     *
     * @return $this
     */
    protected function _syncIndexes()
    {
        $connection = $this->getConnection();
        if (!$connection) {
            return $this;
        }
        $indexes = Mage::getConfig()->getNode(Mage_Index_Model_Process::XML_PATH_INDEXER_DATA);
        $indexCodes = [];
        foreach ($indexes->children() as $code => $index) {
            $indexCodes[] = $code;
        }
        $table = $this->getTable('index/process');
        $select = $connection->select()->from($table, 'indexer_code');
        $existingIndexes = $connection->fetchCol($select);
        $delete = array_diff($existingIndexes, $indexCodes);
        $insert = array_diff($indexCodes, $existingIndexes);

        if (!empty($delete)) {
            $connection->delete($table, $connection->quoteInto('indexer_code IN (?)', $delete));
        }
        if (!empty($insert)) {
            $insertData = [];
            foreach ($insert as $code) {
                $insertData[] = [
                    'indexer_code' => $code,
                    'status' => Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX
                ];
            }
            if (method_exists($connection, 'insertArray')) {
                $connection->insertArray($table, ['indexer_code', 'status'], $insertData);
            }
        }
    }
}
