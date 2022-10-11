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
 * @package    Mage_Eav
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Entity type model
 *
 * @category   Mage
 * @package    Mage_Eav
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @method Mage_Eav_Model_Resource_Entity_Type _getResource()
 * @method Mage_Eav_Model_Resource_Entity_Type getResource()
 * @method Mage_Eav_Model_Resource_Entity_Type_Collection getCollection()
 *
 * @method $this setEntityTypeCode(string $value)
 * @method string getEntityModel()
 * @method $this setEntityModel(string $value)
 * @method $this setAttributeModel(string $value)
 * @method $this setEntityTable(string $value)
 * @method $this setValueTablePrefix(string $value)
 * @method $this setEntityIdField(string $value)
 * @method int getIsDataSharing()
 * @method $this setIsDataSharing(int $value)
 * @method string getDataSharingKey()
 * @method $this setDataSharingKey(string $value)
 * @method $this setDefaultAttributeSetId(int $value)
 * @method string getIncrementModel()
 * @method $this setIncrementModel(string $value)
 * @method int getIncrementPerStore()
 * @method $this setIncrementPerStore(int $value)
 * @method int getIncrementPadLength()
 * @method $this setIncrementPadLength(int $value)
 * @method string getIncrementPadChar()
 * @method $this setIncrementPadChar(string $value)
 * @method string getAdditionalAttributeTable()
 * @method $this setAdditionalAttributeTable(string $value)
 * @method $this setEntityAttributeCollection(string $value)
 * @method $this setAttributeCodes(array $value)
 */
class Mage_Eav_Model_Entity_Type extends Mage_Core_Model_Abstract
{
    /**
     * Collection of attributes
     *
     * @var Mage_Eav_Model_Resource_Entity_Attribute_Collection
     */
    protected $_attributes;

    /**
     * Array of attributes
     *
     * @var array
     */
    protected $_attributesBySet             = [];

    /**
     * Collection of sets
     *
     * @var Mage_Eav_Model_Resource_Entity_Attribute_Set_Collection
     */
    protected $_sets;

    protected function _construct()
    {
        $this->_init('eav/entity_type');
    }

    /**
     * Load type by code
     *
     * @param string $code
     * @return $this
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
     * @return  Mage_Eav_Model_Resource_Entity_Attribute_Collection
     */
    public function getAttributeCollection($setId = null)
    {
        if ($setId === null) {
            if ($this->_attributes === null) {
                $this->_attributes = $this->_getAttributeCollection()
                    ->setEntityTypeFilter($this);
            }
            $collection = $this->_attributes;
        } else {
            if (!isset($this->_attributesBySet[$setId])) {
                $this->_attributesBySet[$setId] = $this->_getAttributeCollection()
                    ->setEntityTypeFilter($this)
                    ->setAttributeSetFilter($setId);
            }
            $collection = $this->_attributesBySet[$setId];
        }

        return $collection;
    }

    /**
     * Init and retrieve attribute collection
     *
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract|object
     */
    protected function _getAttributeCollection()
    {
        $collection = Mage::getModel('eav/entity_attribute')->getCollection();
        $objectsModel = $this->getAttributeModel();
        if ($objectsModel) {
            $collection->setModel($objectsModel);
        }

        return $collection;
    }

    /**
     * Retrieve entity tpe sets collection
     *
     * @return Mage_Eav_Model_Resource_Entity_Attribute_Set_Collection
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
     * Retrieve new incrementId
     *
     * @param int $storeId
     * @return false|string
     * @throws Exception
     */
    public function fetchNewIncrementId($storeId = null)
    {
        if (!$this->getIncrementModel()) {
            return false;
        }

        if (!$this->getIncrementPerStore() || ($storeId === null)) {
            /**
             * store_id null we can have for entity from removed store
             */
            $storeId = 0;
        }

        // Start transaction to run SELECT ... FOR UPDATE
        $this->_getResource()->beginTransaction();

        try {
            $entityStoreConfig = Mage::getModel('eav/entity_store')
                ->loadByEntityStore($this->getId(), $storeId);

            if (!$entityStoreConfig->getId()) {
                $entityStoreConfig
                    ->setEntityTypeId($this->getId())
                    ->setStoreId($storeId)
                    ->setIncrementPrefix($storeId)
                    ->save();
            }

            /** @var Mage_Eav_Model_Entity_Increment_Abstract $incrementInstance */
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
        } catch (Exception $e) {
            $this->_getResource()->rollBack();
            throw $e;
        }

        return $incrementId;
    }

    /**
     * Retrieve entity id field
     *
     * @return string|null
     */
    public function getEntityIdField()
    {
        return $this->_data['entity_id_field'] ?? null;
    }

    /**
     * Retrieve entity table name
     *
     * @return string|null
     */
    public function getEntityTable()
    {
        return $this->_data['entity_table'] ?? null;
    }

    /**
     * Retrieve entity table prefix name
     *
     * @return string|null
     */
    public function getValueTablePrefix()
    {
        $prefix = $this->getEntityTablePrefix();
        if ($prefix) {
            return $this->getResource()->getTable($prefix);
        }

        return null;
    }

    /**
     * Retrieve entity table prefix
     *
     * @return string
     */
    public function getEntityTablePrefix()
    {
        $tablePrefix = trim($this->_data['value_table_prefix']);

        if (empty($tablePrefix)) {
            $tablePrefix = $this->getEntityTable();
        }

        return $tablePrefix;
    }

    /**
     * Get default attribute set identifier for etity type
     *
     * @return string|null
     */
    public function getDefaultAttributeSetId()
    {
        return $this->_data['default_attribute_set_id'] ?? null;
    }

    /**
     * Retrieve entity type id
     *
     * @return string|null
     */
    public function getEntityTypeId()
    {
        return $this->_data['entity_type_id'] ?? null;
    }

    /**
     * Retrieve entity type code
     *
     * @return string|null
     */
    public function getEntityTypeCode()
    {
        return $this->_data['entity_type_code'] ?? null;
    }

    /**
     * Retrieve attribute codes
     *
     * @return array|null
     */
    public function getAttributeCodes()
    {
        return $this->_data['attribute_codes'] ?? null;
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

        return $this->_data['attribute_model'];
    }

    /**
     * Retrieve resource entity object
     *
     * @return Mage_Core_Model_Resource_Abstract
     */
    public function getEntity()
    {
        return Mage::getResourceSingleton($this->_data['entity_model']);
    }

    /**
     * Return attribute collection. If not specify return default
     *
     * @return string
     */
    public function getEntityAttributeCollection()
    {
        $collection = $this->_getData('entity_attribute_collection');
        if ($collection) {
            return $collection;
        }
        return 'eav/entity_attribute_collection';
    }
}
