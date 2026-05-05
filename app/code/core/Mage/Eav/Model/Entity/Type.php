<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Eav
 */

/**
 * Entity type model
 *
 * @package    Mage_Eav
 *
 * @method Mage_Eav_Model_Resource_Entity_Type            _getResource()
 * @method Mage_Eav_Model_Resource_Entity_Type_Collection getCollection()
 * @method Mage_Eav_Model_Resource_Entity_Type            getResource()
 * @method Mage_Eav_Model_Resource_Entity_Type_Collection getResourceCollection()
 * @method $this                                          setAttributeCodes(array $value)
 * @method $this                                          setEntityAttributeCollection(string $value)
 * @method $this                                          setIsDataSharing(int $value)
 * @method $this                                          setValueTablePrefix(string $value)
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

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('eav/entity_type');
    }

    /**
     * Load type by code
     *
     * @param  string $code
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
     * @param  null|int                                            $setId
     * @return Mage_Eav_Model_Resource_Entity_Attribute_Collection
     */
    public function getAttributeCollection($setId = null)
    {
        if ($setId === null && $this->_attributes !== null) {
            return $this->_attributes;
        }

        if (isset($this->_attributesBySet[$setId])) {
            return $this->_attributesBySet[$setId];
        }

        $collection = $this->newAttributeCollection($setId);

        if ($setId === null) {
            $this->_attributes = $collection;
        } else {
            $this->_attributesBySet[$setId] = $collection;
        }

        return $collection;
    }

    /**
     * Create entity type attributes collection
     *
     * @param  null|int                                            $setId
     * @return Mage_Eav_Model_Resource_Entity_Attribute_Collection
     */
    public function newAttributeCollection($setId = null)
    {
        $collection = $this->_getAttributeCollection()
            ->setEntityTypeFilter($this);

        if ($setId !== null) {
            $collection->setAttributeSetFilter($setId);
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
        $collectionClass = $this->getEntityAttributeCollection();
        $collection = Mage::getResourceModel($collectionClass);
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
        if (is_null($this->_sets)) {
            $this->_sets = Mage::getModel('eav/entity_attribute_set')->getResourceCollection()
                ->setEntityTypeFilter($this->getId());
        }

        return $this->_sets;
    }

    /**
     * Retrieve new incrementId
     *
     * @param  int          $storeId
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
            $id = $this->getId();
            $entityStoreConfig = Mage::getModel('eav/entity_store')
                ->loadByEntityStore($id, $storeId);

            if (!$entityStoreConfig->getId()) {
                $entityStoreConfig
                    ->setEntityTypeId($id)
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
        } catch (Exception $exception) {
            $this->_getResource()->rollBack();
            throw $exception;
        }

        return $incrementId;
    }

    /**
     * Retrieve entity id field
     *
     * @return null|string
     */
    public function getEntityIdField()
    {
        return $this->_data['entity_id_field'] ?? null;
    }

    /**
     * Retrieve entity table name
     *
     * @return null|string
     */
    public function getEntityTable()
    {
        return $this->_data['entity_table'] ?? null;
    }

    /**
     * Retrieve entity table prefix name
     *
     * @return null|string
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
        $tablePrefix = trim((string) $this->_data['value_table_prefix']);

        if (empty($tablePrefix)) {
            return $this->getEntityTable();
        }

        return $tablePrefix;
    }

    /**
     * Get default attribute set identifier for entity type
     *
     * @return null|int
     */
    public function getDefaultAttributeSetId()
    {
        return isset($this->_data['default_attribute_set_id']) ? (int) $this->_data['default_attribute_set_id'] : null;
    }

    /**
     * Retrieve entity type id
     *
     * @return null|int
     */
    public function getEntityTypeId()
    {
        return isset($this->_data['entity_type_id']) ? (int) $this->_data['entity_type_id'] : null;
    }

    /**
     * Retrieve entity type code
     *
     * @return null|string
     */
    public function getEntityTypeCode()
    {
        return $this->_data['entity_type_code'] ?? null;
    }

    /**
     * Retrieve attribute codes
     *
     * @return null|array
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
     * @return Mage_Eav_Model_Entity_Abstract
     */
    public function getEntity()
    {
        /** @var Mage_Eav_Model_Entity_Abstract $entity */
        $entity = Mage::getResourceSingleton($this->_data['entity_model']);
        return $entity;
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

    public function getAdditionalAttributeTable(): string
    {
        return (string) $this->_getData('additional_attribute_table');
    }

    public function getDataSharingKey(): string
    {
        return (string) $this->_getData('data_sharing_key');
    }

    public function getEntityModel(): string
    {
        return (string) $this->_getData('entity_model');
    }

    public function getIncrementModel(): string
    {
        return (string) $this->_getData('increment_model');
    }

    public function getIncrementPadChar(): string
    {
        return (string) $this->_getData('increment_pad_char');
    }

    public function getIncrementPadLength(): int
    {
        return (int) $this->_getData('increment_pad_length');
    }

    public function getIncrementPerStore(): int
    {
        return (int) $this->_getData('increment_per_store');
    }

    public function getIsDataSharing(): int
    {
        return (int) $this->_getData('is_data_sharing');
    }

    public function setAdditionalAttributeTable(string $value): static
    {
        return $this->setData('additional_attribute_table', $value);
    }

    public function setAttributeModel(string $value): static
    {
        return $this->setData('attribute_model', $value);
    }

    public function setDataSharingKey(string $value): static
    {
        return $this->setData('data_sharing_key', $value);
    }

    public function setDefaultAttributeSetId(int $value): static
    {
        return $this->setData('default_attribute_set_id', $value);
    }

    public function setEntityIdField(string $value): static
    {
        return $this->setData('entity_id_field', $value);
    }

    public function setEntityModel(string $value): static
    {
        return $this->setData('entity_model', $value);
    }

    public function setEntityTable(string $value): static
    {
        return $this->setData('entity_table', $value);
    }

    public function setEntityTypeCode(string $value): static
    {
        return $this->setData('entity_type_code', $value);
    }

    public function setIncrementModel(string $value): static
    {
        return $this->setData('increment_model', $value);
    }

    public function setIncrementPadChar(string $value): static
    {
        return $this->setData('increment_pad_char', $value);
    }

    public function setIncrementPadLength(int $value): static
    {
        return $this->setData('increment_pad_length', $value);
    }

    public function setIncrementPerStore(int $value): static
    {
        return $this->setData('increment_per_store', $value);
    }
}
