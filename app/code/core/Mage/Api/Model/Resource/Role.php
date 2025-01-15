<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Api
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * ACL role resource
 *
 * @category   Mage
 * @package    Mage_Api
 */
class Mage_Api_Model_Resource_Role extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('api/role', 'role_id');
    }

    /**
     * Action before save
     *
     * @return $this
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        $now = Varien_Date::now();
        if (!$object->getId()) {
            $object->setCreated($now);
        }
        $object->setModified($now);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function load(Mage_Core_Model_Abstract $object, $value, $field = null)
    {
        if (!(int) $value && is_string($value)) {
            $field = 'role_id';
        }
        return parent::load($object, $value, $field);
    }
}
