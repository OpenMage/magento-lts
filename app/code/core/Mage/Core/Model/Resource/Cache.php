<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2017-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
