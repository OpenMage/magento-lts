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
 * @package    Mage_Dataflow
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Convert profile resource model
 *
 * @category   Mage
 * @package    Mage_Dataflow
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Dataflow_Model_Resource_Profile extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('dataflow/profile', 'profile_id');
    }

    /**
     * Setting up created_at and updarted_at
     *
     * @inheritDoc
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getCreatedAt()) {
            $object->setCreatedAt($this->formatDate(time()));
        }
        $object->setUpdatedAt($this->formatDate(time()));
        return parent::_beforeSave($object);
    }

    /**
     * Returns true if profile with name exists
     *
     * @param string $name
     * @param int $id
     * @return bool
     */
    public function isProfileExists($name, $id = null)
    {
        $bind = ['name' => $name];
        $select = $this->_getReadAdapter()->select();
        $select
            ->from($this->getMainTable(), 'count(1)')
            ->where('name = :name');
        if ($id) {
            $select->where("{$this->getIdFieldName()} != :id");
            $bind['id'] = $id;
        }
        return $this->_getReadAdapter()->fetchOne($select, $bind) ? true : false;
    }
}
