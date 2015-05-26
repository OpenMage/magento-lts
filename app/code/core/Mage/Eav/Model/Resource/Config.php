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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Eav
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Eav Resource Config model
 *
 * @category    Mage
 * @package     Mage_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Eav_Model_Resource_Config extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Array of entity types
     *
     * @var array
     */
    protected static $_entityTypes   = array();

    /**
     * Array of attributes
     *
     * @var array
     */
    protected static $_attributes    = array();

    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('eav/entity_type', 'entity_type_id');
    }

    /**
     * Load all entity types
     *
     * @return Mage_Eav_Model_Resource_Config
     */
    protected function _loadTypes()
    {
        $adapter = $this->_getReadAdapter();
        if (!$adapter) {
            return $this;
        }
        if (empty(self::$_entityTypes)) {
            $select = $adapter->select()->from($this->getMainTable());
            $data   = $adapter->fetchAll($select);
            foreach ($data as $row) {
                self::$_entityTypes['by_id'][$row['entity_type_id']] = $row;
                self::$_entityTypes['by_code'][$row['entity_type_code']] = $row;
            }
        }

        return $this;
    }

    /**
     * Load attribute types
     *
     * @param ind $typeId
     * @return array
     */
    protected function _loadTypeAttributes($typeId)
    {
        if (!isset(self::$_attributes[$typeId])) {
            $adapter = $this->_getReadAdapter();
            $bind    = array('entity_type_id' => $typeId);
            $select  = $adapter->select()
                ->from($this->getTable('eav/attribute'))
                ->where('entity_type_id = :entity_type_id');

            self::$_attributes[$typeId] = $adapter->fetchAll($select, $bind);
        }

        return self::$_attributes[$typeId];
    }

    /**
     * Retrieve entity type data
     *
     * @param string $entityType
     * @return array
     */
    public function fetchEntityTypeData($entityType)
    {
        $this->_loadTypes();

        if (is_numeric($entityType)) {
            $info = isset(self::$_entityTypes['by_id'][$entityType])
                ? self::$_entityTypes['by_id'][$entityType] : null;
        } else {
            $info = isset(self::$_entityTypes['by_code'][$entityType])
                ? self::$_entityTypes['by_code'][$entityType] : null;
        }

        $data = array();
        if ($info) {
            $data['entity']     = $info;
            $attributes         = $this->_loadTypeAttributes($info['entity_type_id']);
            $data['attributes'] = array();
            foreach ($attributes as $attribute) {
                $data['attributes'][$attribute['attribute_id']] = $attribute;
                $data['attributes'][$attribute['attribute_code']] = $attribute['attribute_id'];
            }
        }

        return $data;
    }
}
