<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Index
 */

/**
 * Index Setup Model
 *
 * @package    Mage_Index
 */
class Mage_Index_Model_Resource_Setup extends Mage_Core_Model_Resource_Setup
{
    /**
     * Apply Index module DB updates and sync indexes declaration
     *
     * @return $this
     */
    public function applyUpdates()
    {
        parent::applyUpdates();
        $this->_syncIndexes();

        return $this;
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
                    'status' => Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX,
                ];
            }
            if (method_exists($connection, 'insertArray')) {
                $connection->insertArray($table, ['indexer_code', 'status'], $insertData);
            }
        }

        return $this;
    }
}
