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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Eav
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Entity type model
 *
 * @category   Mage
 * @package    Mage_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Eav_Model_Entity_Type extends Mage_Core_Model_Abstract
{

    /**
     * Enter description here...
     *
     * @var Mage_Eav_Model_Mysql4_Entity_Attribute_Collection
     */
    protected $_attributes;

    /**
     * Enter description here...
     *
     * @var array
     */
    protected $_attributesBySet = array();

    /**
     * Enter description here...
     *
     * @var Mage_Eav_Model_Mysql4_Entity_Attribute_Set_Collection
     */
    protected $_sets;

    /**
     * Enter description here...
     *
     */
    protected function _construct()
    {
        $this->_init('eav/entity_type');
    }

    /**
     * Enter description here...
     *
     * @param string $code
     * @return Mage_Eav_Model_Entity_Type
     */
    public function loadByCode($code)
    {
        $this->_getResource()->loadByCode($this, $code);
        $this->_afterLoad();
        return $this;
    }

    /**
     * Retrieve entity type attributes collection
     *
     * @param   int $setId
     * @return  Mage_Eav_Model_Mysql4_Entity_Attribute_Collection
     */
    public function getAttributeCollection($setId = null)
    {
        if (is_null($setId)) {
            if (is_null($this->_attributes)) {
                $this->_attributes = $this->_getAttributeCollection()
                    ->setEntityTypeFilter($this->getId());
            }
            $collection = $this->_attributes;
        }
        else {
            if (!isset($this->_attributesBySet[$setId])) {
                $this->_attributesBySet[$setId] = $this->_getAttributeCollection()
                    ->setEntityTypeFilter($this->getId())
                    ->setAttributeSetFilter($setId);
            }
            $collection = $this->_attributesBySet[$setId];
        }
        return $collection;
    }

    /**
     * Enter description here...
     *
     * @return Mage_Eav_Model_Mysql4_Entity_Attribute_Collection
     */
    protected function _getAttributeCollection()
    {
        $collection = Mage::getModel('eav/entity_attribute')->getCollection();
        if ($objectsModel = $this->getAttributeModel()) {
            $collection->setModel($objectsModel);
        }
        return $collection;
    }

    /**
     * Retrieve entity tpe sets collection
     *
     * @return Mage_Eav_Model_Mysql4_Entity_Attribute_Set_Collection
     */
    public function getAttributeSetCollection()
    {
        if (empty($this->_sets)) {
            $this->_sets = Mage::getModel('eav/entity_attribute_set')->getResourceCollection()
                ->setEntityTypeFilter($this->getId());
        }
        return $this->_sets;
    }

    /**
     * Enter description here...
     *
     * @param int $storeId
     * @return string
     */
    public function fetchNewIncrementId($storeId=null)
    {
        if (!$this->getIncrementModel()) {
            return false;
        }

        if (!$this->getIncrementPerStore()) {
            $storeId = 0;
        }
        elseif (is_null($storeId)) {
            /**
             * store_id null we can have for entity from removed store
             */
            $storeId = 0;
            //throw Mage::exception('Mage_Eav', Mage::helper('eav')->__('Valid store_id is expected!'));
        }

        // Start transaction to run SELECT ... FOR UPDATE
        $this->_getResource()->beginTransaction();

        $entityStoreConfig = Mage::getModel('eav/entity_store')
            ->loadByEntityStore($this->getId(), $storeId);

        if (!$entityStoreConfig->getId()) {
            $entityStoreConfig
                ->setEntityTypeId($this->getId())
                ->setStoreId($storeId)
                ->setIncrementPrefix($storeId)
                ->save();
        }

        $incrementInstance = Mage::getModel($this->getIncrementModel())
            ->setPrefix($entityStoreConfig->getIncrementPrefix())
            ->setPadLength($this->getIncrementPadLength())
            ->setPadChar($this->getIncrementPadChar())
            ->setLastId($entityStoreConfig->getIncrementLastId())
            ->setEntityTypeId($entityStoreConfig->getEntityTypeId())
            ->setStoreId($entityStoreConfig->getStoreId());

        /**
         * do read lock on eav/entity_store to solve potential timing issues
         * (most probably already done by beginTransaction of entity save)
         */
        $incrementId = $incrementInstance->getNextId();
        $entityStoreConfig->setIncrementLastId($incrementId);
        $entityStoreConfig->save();

        // Commit increment_last_id changes
        $this->_getResource()->commit();

        return $incrementId;
    }

    /**
     * Enter description here...
     *
     * @return string
     */
    public function getEntityIdField()
    {
        return isset($this->_data['entity_id_field']) ? $this->_data['entity_id_field'] : null;
    }

    /**
     * Enter description here...
     *
     * @return string
     */
    public function getEntityTable()
    {
        return isset($this->_data['entity_table']) ? $this->_data['entity_table'] : null;
    }

    /**
     * Enter description here...
     *
     * @return string
     */
    public function getValueTablePrefix()
    {
        if (empty($this->_data['value_table_prefix'])) {
            $this->_data['value_table_prefix'] = $this->_getResource()->getTable($this->getEntityTable());
        }
        return $this->_data['value_table_prefix'];
    }

    /**
     * Get default attribute set identifier for etity type
     *
     * @return string
     */
    public function getDefaultAttributeSetId()
    {
        return isset($this->_data['default_attribute_set_id']) ? $this->_data['default_attribute_set_id'] : null;
    }

    /**
     * Enter description here...
     *
     * @return string
     */
    public function getEntityTypeId()
    {
        return isset($this->_data['entity_type_id']) ? $this->_data['entity_type_id'] : null;
    }

    public function getEntityTypeCode()
    {
        return isset($this->_data['entity_type_code']) ? $this->_data['entity_type_code'] : null;
    }

    public function getAttributeCodes()
    {
        return isset($this->_data['attribute_codes']) ? $this->_data['attribute_codes'] : null;
    }

    /**
     * Get attribute model code for entity type
     *
     * @return string
     */
    public function getAttributeModel()
    {
        if (empty($this->_data['attribute_model'])) {
            return Mage_Eav_Model_Entity::DEFAULT_ATTRIBUTE_MODEL;
        }
        else {
            return $this->_data['attribute_model'];
        }
    }

    public function getEntity()
    {
        return Mage::getResourceSingleton($this->_data['entity_model']);
    }
}
