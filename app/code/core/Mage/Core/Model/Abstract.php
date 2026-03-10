<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * Abstract model class
 *
 * @package    Mage_Core
 *
 * @method Mage_Customer_Model_Address_Abstract getBillingAddress()
 * @method string                               getCreatedAt()
 * @method Mage_Customer_Model_Address_Abstract getShippingAddress()
 * @method string                               getUpdatedAt()
 * @method bool                                 hasErrors()
 * @method $this                                setAttribute(Mage_Eav_Model_Entity_Attribute_Abstract $value)
 * @method $this                                setCreatedAt(null|string $currentTime)
 * @method $this                                setUpdatedAt(null|string $currentTime)
 */
abstract class Mage_Core_Model_Abstract extends Varien_Object
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'core_abstract';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'object';

    /**
     * Original data that was loaded
     *
     * @var null|array
     */
    protected $_origData;

    /**
     * Name of the resource model
     *
     * @var string
     */
    protected $_resourceName;

    /**
     * Resource model instance
     *
     * @var Mage_Core_Model_Resource_Db_Abstract
     */
    protected $_resource;

    /**
     * Name of the resource collection model
     *
     * @var string
     */
    protected $_resourceCollectionName;

    /**
     * Model cache tag for clear cache in after save and after delete
     *
     * When you use true - all cache will be clean
     *
     * @var array|bool|string
     */
    protected $_cacheTag    = false;

    /**
     * Flag which can stop data saving after before save
     * Can be used for next sequence: we check data in _beforeSave, if data are
     * not valid - we can set this flag to false value and save process will be stopped
     *
     * @var bool
     */
    protected $_dataSaveAllowed = true;

    /**
     * Flag which allow detect object state: is it new object (without id) or existing one (with id)
     *
     * @var null|bool
     */
    protected $_isObjectNew     = null;

    /**
     * Standard model initialization
     *
     * @param string $resourceModel
     */
    protected function _init($resourceModel)
    {
        $this->_setResourceModel($resourceModel);
    }

    /**
     * Get object loaded data (original data)
     *
     * @param  string $key
     * @return mixed
     */
    public function getOrigData($key = null)
    {
        if (is_null($key)) {
            return $this->_origData;
        }

        return $this->_origData[$key] ?? null;
    }

    /**
     * Initialize object original data
     *
     * @param  string $key
     * @param  mixed  $data
     * @return $this
     */
    public function setOrigData($key = null, $data = null)
    {
        if (is_null($key)) {
            $this->_origData = $this->_data;
        } else {
            $this->_origData[$key] = $data;
        }

        return $this;
    }

    /**
     * Compare object data with original data
     *
     * @param  string $field
     * @return bool
     */
    public function dataHasChangedFor($field)
    {
        $newData = $this->getData($field);
        $origData = $this->getOrigData($field);

        return $newData != $origData;
    }

    /**
     * Set resource names
     *
     * If collection name is omitted, resource name will be used with _collection appended
     *
     * @param string      $resourceName
     * @param null|string $resourceCollectionName
     */
    protected function _setResourceModel($resourceName, $resourceCollectionName = null)
    {
        $this->_resourceName = $resourceName;
        if (is_null($resourceCollectionName)) {
            $resourceCollectionName = $resourceName . '_collection';
        }

        $this->_resourceCollectionName = $resourceCollectionName;
    }

    /**
     * Get resource instance
     *
     * @return Mage_Core_Model_Resource_Db_Abstract|object
     * @throws Mage_Core_Exception
     */
    protected function _getResource()
    {
        if (is_null($this->_resourceName)) {
            Mage::throwException(Mage::helper('core')->__('Resource is not set.'));
        }

        $resource = Mage::getResourceSingleton($this->_resourceName);
        if (!$resource) {
            Mage::throwException(Mage::helper('core')->__('Resource "%s" is not found.', $this->_resourceName));
        }

        return $resource;
    }

    /**
     * Retrieve identifier field name for model
     *
     * @return string
     * @throws Mage_Core_Exception
     */
    public function getIdFieldName()
    {
        if (!($fieldName = parent::getIdFieldName())) {
            $fieldName = $this->_getResource()->getIdFieldName();
            $this->setIdFieldName($fieldName);
        }

        return $fieldName;
    }

    /**
     * Retrieve model object identifier
     *
     * @return null|int|string
     * @throws Mage_Core_Exception
     */
    public function getId()
    {
        $fieldName = $this->getIdFieldName();
        if ($fieldName) {
            return $this->_getData($fieldName);
        }

        return $this->_getData('id');
    }

    /**
     * Declare model object identifier value
     *
     * @param  mixed               $value
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function setId($value)
    {
        if ($this->getIdFieldName()) {
            $this->setData($this->getIdFieldName(), $value);
        } else {
            $this->setData('id', $value);
        }

        return $this;
    }

    /**
     * Retrieve model resource name
     *
     * @return string
     */
    public function getResourceName()
    {
        return $this->_resourceName;
    }

    /**
     * Get collection instance
     *
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract
     * @throws Mage_Core_Exception
     */
    public function getResourceCollection()
    {
        if (is_null($this->_resourceCollectionName)) {
            Mage::throwException(Mage::helper('core')->__('Model collection resource name is not defined.'));
        }

        $resource = Mage::getResourceModel($this->_resourceCollectionName, $this->_getResource());
        if (!$resource) {
            Mage::throwException(Mage::helper('core')->__('Resource "%s" is not found.', $this->_resourceCollectionName));
        }

        return $resource;
    }

    /**
     * @return false|Mage_Core_Model_Resource_Db_Collection_Abstract
     * @throws Mage_Core_Exception
     */
    public function getCollection()
    {
        return $this->getResourceCollection();
    }

    /**
     * Load object data
     *
     * @param  null|int|string     $id
     * @param  null|string         $field
     * @return $this
     * @throws Exception
     * @throws Mage_Core_Exception
     */
    public function load($id, $field = null)
    {
        $this->_beforeLoad($id, $field);
        $this->_getResource()->load($this, $id, $field);
        $this->_afterLoad();
        $this->setOrigData();
        $this->_hasDataChanges = false;
        return $this;
    }

    /**
     * Get array of objects transferred to default events processing
     *
     * @return array
     */
    protected function _getEventData()
    {
        return [
            'data_object'       => $this,
            $this->_eventObject => $this,
        ];
    }

    /**
     * Processing object before load data
     *
     * @param  int         $id
     * @param  null|string $field
     * @return $this
     */
    protected function _beforeLoad($id, $field = null)
    {
        $params = ['object' => $this, 'field' => $field, 'value' => $id];
        Mage::dispatchEvent('model_load_before', $params);
        $params = array_merge($params, $this->_getEventData());
        Mage::dispatchEvent($this->_eventPrefix . '_load_before', $params);
        return $this;
    }

    /**
     * Processing object after load data
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        Mage::dispatchEvent('model_load_after', ['object' => $this]);
        Mage::dispatchEvent($this->_eventPrefix . '_load_after', $this->_getEventData());
        return $this;
    }

    /**
     * Object after load processing. Implemented as public interface for supporting objects after load in collections
     *
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function afterLoad()
    {
        $this->getResource()->afterLoad($this);
        $this->_afterLoad();
        return $this;
    }

    /**
     * Check whether model has changed data.
     * Can be overloaded in child classes to perform advanced check whether model needs to be saved
     * e.g. using resourceModel->hasDataChanged() or any other technique
     *
     * @return bool
     */
    protected function _hasModelChanged()
    {
        return $this->hasDataChanges();
    }

    /**
     * Save object data
     *
     * @return $this
     * @throws Throwable
     */
    public function save()
    {
        /**
         * Direct deleted items to delete method
         */
        if ($this->isDeleted()) {
            return $this->delete();
        }

        if (!$this->_hasModelChanged()) {
            return $this;
        }

        $this->_getResource()->beginTransaction();

        try {
            $this->_beforeSave();
            if ($this->_dataSaveAllowed) {
                $this->_getResource()->save($this);
                $this->_afterSave();
            }

            $this->_getResource()->addCommitCallback([$this, 'afterCommitCallback'])
                ->commit();
            $this->_hasDataChanges = false;
        } catch (Throwable $throwable) {
            $this->_getResource()->rollBack();
            $this->_hasDataChanges = true;
            throw $throwable;
        }

        return $this;
    }

    /**
     * Callback function which called after transaction commit in resource model
     *
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function afterCommitCallback()
    {
        $this->cleanModelCache();
        Mage::dispatchEvent('model_save_commit_after', ['object' => $this]);
        Mage::dispatchEvent($this->_eventPrefix . '_save_commit_after', $this->_getEventData());
        return $this;
    }

    /**
     * Check object state (true - if it is object without id on object just created)
     * This method can help detect if object just created in _afterSave method
     * problem is what in after save object has id and we can't detect what object was
     * created in this transaction
     *
     * @param  bool                $flag
     * @return bool
     * @throws Mage_Core_Exception
     */
    public function isObjectNew($flag = null)
    {
        if ($flag !== null) {
            $this->_isObjectNew = $flag;
        }

        return $this->_isObjectNew ?? !(bool) $this->getId();
    }

    /**
     * Processing object before save data
     *
     * @return $this
     * @throws Mage_Core_Exception
     */
    protected function _beforeSave()
    {
        if (!$this->getId()) {
            $this->isObjectNew(true);
        }

        Mage::dispatchEvent('model_save_before', ['object' => $this]);
        Mage::dispatchEvent($this->_eventPrefix . '_save_before', $this->_getEventData());
        return $this;
    }

    /**
     * Get list of cache tags applied to model object.
     * Return false if cache tags are not supported by model
     *
     * @return array|false
     * @throws Mage_Core_Exception
     */
    public function getCacheTags()
    {
        $tags = false;
        if ($this->_cacheTag) {
            if ($this->_cacheTag === true) {
                $tags = [];
            } else {
                if (is_array($this->_cacheTag)) {
                    $tags = $this->_cacheTag;
                } else {
                    $tags = [$this->_cacheTag];
                }

                $idTags = $this->getCacheIdTags();
                if ($idTags) {
                    $tags = array_merge($tags, $idTags);
                }
            }
        }

        return $tags;
    }

    /**
     * Get cache tags associated with object id
     *
     * @return array|false
     * @throws Mage_Core_Exception
     */
    public function getCacheIdTags()
    {
        $tags = false;
        if ($this->getId() && $this->_cacheTag) {
            $tags = [];
            if (is_array($this->_cacheTag)) {
                foreach ($this->_cacheTag as $tag) {
                    $tags[] = $tag . '_' . $this->getId();
                }
            } else {
                $tags[] = $this->_cacheTag . '_' . $this->getId();
            }
        }

        return $tags;
    }

    /**
     * Remove model object related cache
     *
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function cleanModelCache()
    {
        $tags = $this->getCacheTags();
        if ($tags !== false) {
            Mage::app()->cleanCache($tags);
        }

        return $this;
    }

    /**
     * Processing object after save data
     *
     * @return $this
     */
    protected function _afterSave()
    {
        Mage::dispatchEvent('model_save_after', ['object' => $this]);
        Mage::dispatchEvent($this->_eventPrefix . '_save_after', $this->_getEventData());
        return $this;
    }

    /**
     * Delete object from database
     *
     * @return $this
     * @throws Throwable
     */
    public function delete()
    {
        $this->_getResource()->beginTransaction();
        try {
            $this->_beforeDelete();
            $this->_getResource()->delete($this);
            $this->_afterDelete();

            $this->_getResource()->commit();
        } catch (Throwable $throwable) {
            $this->_getResource()->rollBack();
            throw $throwable;
        }

        $this->_afterDeleteCommit();
        return $this;
    }

    /**
     * Processing object before delete data
     *
     * @return $this
     * @throws Mage_Core_Exception
     */
    protected function _beforeDelete()
    {
        Mage::dispatchEvent('model_delete_before', ['object' => $this]);
        Mage::dispatchEvent($this->_eventPrefix . '_delete_before', $this->_getEventData());
        $this->cleanModelCache();
        return $this;
    }

    /**
     * Safeguard func that will check, if we are in admin area
     *
     * @throws Mage_Core_Exception
     */
    protected function _protectFromNonAdmin()
    {
        if (Mage::registry('isSecureArea')) {
            return;
        }

        if (!Mage::app()->getStore()->isAdmin()) {
            Mage::throwException(Mage::helper('core')->__('Cannot complete this operation from non-admin area.'));
        }
    }

    /**
     * Processing object after delete data
     *
     * @return $this
     */
    protected function _afterDelete()
    {
        Mage::dispatchEvent('model_delete_after', ['object' => $this]);
        Mage::dispatchEvent($this->_eventPrefix . '_delete_after', $this->_getEventData());
        return $this;
    }

    /**
     * Processing manipulation after main transaction commit
     *
     * @return $this
     */
    protected function _afterDeleteCommit()
    {
        Mage::dispatchEvent('model_delete_commit_after', ['object' => $this]);
        Mage::dispatchEvent($this->_eventPrefix . '_delete_commit_after', $this->_getEventData());
        return $this;
    }

    /**
     * Retrieve model resource
     *
     * @return Mage_Core_Model_Resource_Db_Abstract
     * @throws Mage_Core_Exception
     */
    public function getResource()
    {
        return $this->_getResource();
    }

    /**
     * @return int
     */
    public function getEntityId()
    {
        return $this->_getData('entity_id');
    }

    /**
     * Clearing object for correct deleting by garbage collector
     *
     * @return $this
     */
    final public function clearInstance()
    {
        $this->_clearReferences();
        Mage::dispatchEvent($this->_eventPrefix . '_clear', $this->_getEventData());
        $this->_clearData();
        return $this;
    }

    /**
     * Clearing cyclic references
     *
     * @return $this
     */
    protected function _clearReferences()
    {
        return $this;
    }

    /**
     * Clearing object's data
     *
     * @return $this
     */
    protected function _clearData()
    {
        return $this;
    }

    public function isModuleEnabled(string $moduleName, string $helperAlias = 'core'): bool
    {
        return Mage::helper($helperAlias)->isModuleEnabled($moduleName);
    }

    protected function getValidationHelper(): Mage_Core_Helper_Validate
    {
        /** @var Mage_Core_Helper_Validate $validator */
        $validator = Mage::helper('core/validate');
        return $validator;
    }
}
