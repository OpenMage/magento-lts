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
 * EAV entity type resource model
 *
 * @category   Mage
 * @package    Mage_Eav
 */
class Mage_Eav_Model_Resource_Entity_Type extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('eav/entity_type', 'entity_type_id');
    }

    /**
     * Load Entity Type by Code
     *
     * @param Mage_Core_Model_Abstract $object
     * @param string $code
     * @return $this
     */
    public function loadByCode($object, $code)
    {
        return $this->load($object, $code, 'entity_type_code');
    }

    /**
     * Retrieve additional attribute table name for specified entity type
     *
     * @param int $entityTypeId
     * @return string
     */
    public function getAdditionalAttributeTable($entityTypeId)
    {
        $adapter = $this->_getReadAdapter();
        $bind    = ['entity_type_id' => $entityTypeId];
        $select  = $adapter->select()
            ->from($this->getMainTable(), ['additional_attribute_table'])
            ->where('entity_type_id = :entity_type_id');

        return $adapter->fetchOne($select, $bind);
    }
}
