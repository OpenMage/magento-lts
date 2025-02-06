<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Core
 */

/**
 * Core Cache resource model
 *
 * @category   Mage
 * @package    Mage_Core
 */
class Mage_Core_Model_Resource_Cache extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('core/cache_option', 'code');
    }

    /**
     * Get all cache options
     *
     * @return array | false
     */
    public function getAllOptions()
    {
        $adapter = $this->_getReadAdapter();
        if ($adapter) {
            /**
             * Check if table exist (it protect upgrades. cache settings checked before upgrades)
             */
            if ($adapter->isTableExists($this->getMainTable())) {
                $select = $adapter->select()
                    ->from($this->getMainTable(), ['code', 'value']);
                return $adapter->fetchPairs($select);
            }
        }
        return false;
    }

    /**
     * Save all options to option table
     *
     * @param array $options
     * @return $this
     * @throws Exception
     */
    public function saveAllOptions($options)
    {
        $adapter = $this->_getWriteAdapter();
        if (!$adapter) {
            return $this;
        }

        $data = [];
        foreach ($options as $code => $value) {
            $data[] = [$code, $value];
        }

        $adapter->beginTransaction();
        try {
            $this->_getWriteAdapter()->delete($this->getMainTable());
            if ($data) {
                $this->_getWriteAdapter()->insertArray($this->getMainTable(), ['code', 'value'], $data);
            }
            $adapter->commit();
        } catch (Exception $e) {
            $adapter->rollBack();
            throw $e;
        }

        return $this;
    }
}
