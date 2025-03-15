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
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Core config data resource model
 *
 * @category   Mage
 * @package    Mage_Core
 */
class Mage_Core_Model_Resource_Config_Data extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('core/config_data', 'config_id');
    }

    /**
     * Convert array to comma separated value
     *
     * @param Mage_Core_Model_Config_Data $object
     * @inheritDoc
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getId()) {
            $this->_checkUnique($object);
        }

        if (is_array($object->getValue())) {
            $object->setValue(implode(',', $object->getValue()));
        }
        return parent::_beforeSave($object);
    }

    /**
     * Validate unique configuration data before save
     * Set id to object if exists configuration instead of throw exception
     *
     * @param Mage_Core_Model_Config_Data $object
     * @inheritDoc
     */
    protected function _checkUnique(Mage_Core_Model_Abstract $object)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), [$this->getIdFieldName()])
            ->where('scope = :scope')
            ->where('scope_id = :scope_id')
            ->where('path = :path');
        $bind   = [
            'scope'     => $object->getScope(),
            'scope_id'  => $object->getScopeId(),
            'path'      => $object->getPath(),
        ];

        $configId = $this->_getReadAdapter()->fetchOne($select, $bind);
        if ($configId) {
            $object->setId($configId);
        }

        return $this;
    }
}
