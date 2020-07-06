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
 * @package     Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Abstract model class
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 *
 * @method string getCreatedAt()
 * @method $this setCreatedAt(string $currentTime)
 * @method $this setUpdatedAt(string $currentTime)
 * @method $this setAttribute(Mage_Eav_Model_Entity_Attribute_Abstract $value)
 * @method bool hasErrors()
 * @method Mage_Customer_Model_Address_Abstract getBillingAddress()
 * @method Mage_Customer_Model_Address_Abstract getShippingAddress()
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
     * Name of the resource model
     *
     * @var string
     */
    protected $_resourceName;

    /**
     * Resource model instance
     *
     * @var Mage_Core_Model_Mysql4_Abstract
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
     * @var string|true
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
     * @var bool
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
     * Set resource names
     *
     * If collection name is ommited, resource name will be used with _collection appended
     *
     * @param string $resourceName
     * @param string|null $resourceCollectionName
     */
    protected function _setResourceModel($resourceName, $resourceCollectionName = null)
    {
        $this->_resourceName = $resourceName;
        if (is_null($resourceCollectionName)) {
            $resourceCollectionName = $resourceName.'_collection';
        }
        $this->_resourceCollectionName = $resourceCollectionName;
    }

    /**
     * Get resource instance
     *
     * @return Mage_Core_Model_Resource_Db_Abstract|object|string
     */
    protected function _getResource()
    {
        if (empty($this->_resourceName)) {
            Mage::throwException(Mage::helper('core')->__('Resource is not set.'));
        }

        return Mage::getResourceSingleton($this->_resourceName);
    }


    /**
     * Retrieve identifier field name for model
     *
     * @return string
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
     * @return mixed
     */
    public function getId()
    {
        $fieldName = $this->getIdFieldName();
        if ($fieldName) {
            return $this->_getData($fieldName);
        } else {
            return $this->_getData('id');
        }
    }

    /**
     * Declare model object identifier value
     *
     * @param   mixed $id
     * @return  $this
     */
    public function setId($id)
    {
        if ($this->getIdFieldName()) {
            $this->setData($this->getIdFieldName(), $id);
        } else {
            $this->setData('id', $id);
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
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract|false
     * @throws Mage_Core_Exception
     */
    public function getResourceCollection()
    {
        if (empty($this->_resourceCollectionName)) {
            Mage::throwException(Mage::helper('core')->__('Model collection resource name is not defined.'));
        }
        return Mage::getResourceModel($this->_resourceCollectionName, $this->_getResource());
    }

    /**
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract|false
     * @throws Mage_Core_Exception
     */
    public function getCollection()
    {
        return $this->getResourceCollection();
    }

    /**
     * Load object data
     *
     * @param string|integer $id
     * @param string|null $field
     * @return $this
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
     * Get array of objects transfered to default events processing
     *
     * @return array
     */
    protected function _getEventData()
    {
        return array(
            'data_object'       => $this,
            $this->_eventObject => $this,
        );
    }

    /**
     * Processing object before load data
     *
     * @param int $id
     * @param string|null $field
     * @return $this
     */
    protected function _beforeLoad($id, $field = null)
    {
        $params = array('object' => $this, 'field' => $field, 'value'=> $id);
        Mage::dispatchEvent('model_load_before', $params);
        $params = array_merge($params, $this->_getEventData());
        Mage::dispatchEvent($this->_eventPrefix.'_load_before', $params);
        return $this;
    }

    /**
     * Processing object after load data
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        Mage::dispatchEvent('model_load_after', array('object'=>$this));
        Mage::dispatchEvent($this->_eventPrefix.'_load_after', $this->_getEventData());
        return $this;
    }

    /**
     * Object after load processing. Implemented as public interface for supporting objects after load in collections
     *
     * @return $this
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
     * e.g. usign resouceModel->hasDataChanged() or any other technique
     *
     * @return boolean
     */
    protected function _hasModelChanged()
    {
        return $this->hasDataChanges();
    }

    /**
     * Save object data
     *
     * @return $this
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
        $dataCommited = false;
        try {
            $this->_beforeSave();
            if ($this->_dataSaveAllowed) {
                $this->_getResource()->save($this);
                $this->_afterSave();
            }
            $this->_getResource()->addCommitCallback(array($this, 'afterCommitCallback'))
                ->commit();
            $this->_hasDataChanges = false;
            $dataCommited = true;
        } catch (Exception $e) {
            $this->_getResource()->rollBack();
            $this->_hasDataChanges = true;
            throw $e;
        }
        if ($dataCommited) {
            $this->_afterSaveCommit();
        }
        return $this;
    }

    /**
     * Callback function which called after transaction commit in resource model
     *
     * @return $this
     */
    public function afterCommitCallback()
    {
        $this->cleanModelCache();
        Mage::dispatchEvent('model_save_commit_after', array('object'=>$this));
        Mage::dispatchEvent($this->_eventPrefix.'_save_commit_after', $this->_getEventData());
        return $this;
    }

    /**
     * Processing data save after transaction commit.
     * When method is called we don't have garantee what transaction was really commited
     *
     * @deprecated after 1.4.0.0 - please use afterCommitCallback instead
     * @return $this
     */
    protected function _afterSaveCommit()
    {
        return $this;
    }

    /**
     * Check object state (true - if it is object without id on object just created)
     * This method can help detect if object just created in _afterSave method
     * problem is what in after save onject has id and we can't detect what object was
     * created in this transaction
     *
     * @param bool $flag
     * @return bool
     */
    public function isObjectNew($flag = null)
    {
        if ($flag !== null) {
            $this->_isObjectNew = $flag;
        }
        if ($this->_isObjectNew !== null) {
            return $this->_isObjectNew;
        }
        return !(bool)$this->getId();
    }

    /**
     * Processing object before save data
     *
     * @return $this
     */
    protected function _beforeSave()
    {
        if (!$this->getId()) {
            $this->isObjectNew(true);
        }
        Mage::dispatchEvent('model_save_before', array('object'=>$this));
        Mage::dispatchEvent($this->_eventPrefix.'_save_before', $this->_getEventData());
        return $this;
    }

    /**
     * Get list of cache tags applied to model object.
     * Return false if cache tags are not supported by model
     *
     * @return array | false
     */
    public function getCacheTags()
    {
        $tags = false;
        if ($this->_cacheTag) {
            if ($this->_cacheTag === true) {
                $tags = array();
            } else {
                if (is_array($this->_cacheTag)) {
                    $tags = $this->_cacheTag;
                } else {
                    $tags = array($this->_cacheTag);
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
     * Get cahce tags associated with object id
     *
     * @return array|bool
     */
    public function getCacheIdTags()
    {
        $tags = false;
        if ($this->getId() && $this->_cacheTag) {
            $tags = array();
            if (is_array($this->_cacheTag)) {
                foreach ($this->_cacheTag as $_tag) {
                    $tags[] = $_tag.'_'.$this->getId();
                }
            } else {
                $tags[] = $this->_cacheTag.'_'.$this->getId();
            }
        }
        return $tags;
    }

    /**
     * Remove model onject related cache
     *
     * @return $this
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
        Mage::dispatchEvent('model_save_after', array('object'=>$this));
        Mage::dispatchEvent($this->_eventPrefix.'_save_after', $this->_getEventData());
        return $this;
    }

    /**
     * Delete object from database
     *
     * @return $this
     */
    public function delete()
    {
        $this->_getResource()->beginTransaction();
        try {
            $this->_beforeDelete();
            $this->_getResource()->delete($this);
            $this->_afterDelete();

            $this->_getResource()->commit();
        } catch (Exception $e) {
            $this->_getResource()->rollBack();
            throw $e;
        }
        $this->_afterDeleteCommit();
        return $this;
    }

    /**
     * Processing object before delete data
     *
     * @return $this
     */
    protected function _beforeDelete()
    {
        Mage::dispatchEvent('model_delete_before', array('object'=>$this));
        Mage::dispatchEvent($this->_eventPrefix.'_delete_before', $this->_getEventData());
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
        Mage::dispatchEvent('model_delete_after', array('object'=>$this));
        Mage::dispatchEvent($this->_eventPrefix.'_delete_after', $this->_getEventData());
        return $this;
    }

    /**
     * Processing manipulation after main transaction commit
     *
     * @return $this
     */
    protected function _afterDeleteCommit()
    {
        Mage::dispatchEvent('model_delete_commit_after', array('object'=>$this));
        Mage::dispatchEvent($this->_eventPrefix.'_delete_commit_after', $this->_getEventData());
         return $this;
    }

    /**
     * Retrieve model resource
     *
     * @return Mage_Core_Model_Resource_Db_Abstract
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
        Mage::dispatchEvent($this->_eventPrefix.'_clear', $this->_getEventData());
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
}
