<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Eav
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Eav Entity store resource model
 *
 * @category   Mage
 * @package    Mage_Eav
 */
class Mage_Eav_Model_Resource_Entity_Store extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('eav/entity_store', 'entity_store_id');
    }

    /**
     * Load an object by entity type and store
     *
     * @param int $entityTypeId
     * @param int $storeId
     * @return bool
     */
    public function loadByEntityStore(Mage_Core_Model_Abstract $object, $entityTypeId, $storeId)
    {
        $adapter = $this->_getWriteAdapter();
        $bind    = [
            ':entity_type_id' => $entityTypeId,
            ':store_id'       => $storeId
        ];
        $select = $adapter->select()
            ->from($this->getMainTable())
            ->forUpdate(true)
            ->where('entity_type_id = :entity_type_id')
            ->where('store_id = :store_id');
        $data = $adapter->fetchRow($select, $bind);

        if (!$data) {
            return false;
        }

        $object->setData($data);

        $this->_afterLoad($object);

        return true;
    }
}
