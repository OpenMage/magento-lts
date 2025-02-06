<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Admin
 */

/**
 * Admin role resource model
 *
 * @category   Mage
 * @package    Mage_Admin
 */
class Mage_Admin_Model_Resource_Role extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('admin/role', 'role_id');
    }

    /**
     * Process role before saving
     *
     * @return $this
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getId()) {
            $object->setCreated($this->formatDate(true));
        }
        $object->setModified($this->formatDate(true));
        return $this;
    }
}
