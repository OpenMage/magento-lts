<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Eav
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Eav_Model_Mysql4_Config extends Mage_Core_Model_Mysql4_Abstract
{
    protected static $_entityTypes;
    protected static $_attributes   = array();

    protected function _construct()
    {
        $this->_init('eav/entity_type', 'entity_type_id');
    }

    /**
     * Load all entity types
     *
     * @return Mage_Eav_Model_Mysql4_Config
     */
    protected function _loadTypes()
    {
        if (!$this->_getReadAdapter()) {
            self::$_entityTypes = array();
            return $this;
        }
        if (is_null(self::$_entityTypes)) {
            self::$_entityTypes = array();
            $select = $this->_getReadAdapter()->select()->from($this->getMainTable());
            $data = $this->_getReadAdapter()->fetchAll($select);
            foreach ($data as $row) {
            	self::$_entityTypes['by_id'][$row['entity_type_id']] = $row;
            	self::$_entityTypes['by_code'][$row['entity_type_code']] = $row;
            }
        }

        return $this;
    }

    protected function _loadTypeAttributes($typeId)
    {
        if (!isset(self::$_attributes[$typeId])) {
            $select = $this->_getReadAdapter()->select()->from($this->getTable('eav/attribute'))
                ->where('entity_type_id=?', $typeId);
            self::$_attributes[$typeId] = $this->_getReadAdapter()->fetchAll($select);
        }
        return self::$_attributes[$typeId];
    }

    public function fetchEntityTypeData($entityType)
    {
        $this->_loadTypes();

        if (is_numeric($entityType)) {
            $info = isset(self::$_entityTypes['by_id'][$entityType]) ? self::$_entityTypes['by_id'][$entityType] : null;
        }
        else {
            $info = isset(self::$_entityTypes['by_code'][$entityType]) ? self::$_entityTypes['by_code'][$entityType] : null;
        }

        $data = array();
        if ($info) {
            $data['entity'] = $info;
            $attributes = $this->_loadTypeAttributes($info['entity_type_id']);
            $data['attributes'] = array();
            foreach ($attributes as $attribute) {
            	$data['attributes'][$attribute['attribute_id']] = $attribute;
            	$data['attributes'][$attribute['attribute_code']] = $attribute['attribute_id'];
            }
        }
        return $data;
    }
}