<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Dataflow
 */

/**
 * Dataflow Batch model
 *
 * @package    Mage_Dataflow
 *
 * @method Mage_Dataflow_Model_Resource_Batch            _getResource()
 * @method string                                        getAdapter()
 * @method Mage_Dataflow_Model_Resource_Batch_Collection getCollection()
 * @method int                                           getProfileId()
 * @method Mage_Dataflow_Model_Resource_Batch            getResource()
 * @method Mage_Dataflow_Model_Resource_Batch_Collection getResourceCollection()
 * @method int                                           getStoreId()
 * @method $this                                         setAdapter(string $value)
 * @method $this                                         setProfileId(int $value)
 * @method $this                                         setStoreId(int $value)
 */
class Mage_Dataflow_Model_Batch extends Mage_Core_Model_Abstract
{
    /**
     * Lifetime abandoned batches
     */
    public const LIFETIME = 86400;

    /**
     * Field list collection array
     *
     * @var array
     */
    protected $_fieldList = [];

    /**
     * Dataflow batch io adapter
     *
     * @var null|Mage_Dataflow_Model_Batch_Io
     */
    protected $_ioAdapter;

    /**
     * Dataflow batch export model
     *
     * @var null|Mage_Dataflow_Model_Batch_Export
     */
    protected $_batchExport;

    /**
     * Dataflow batch import model
     *
     * @var null|Mage_Dataflow_Model_Batch_Import
     */
    protected $_batchImport;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('dataflow/batch');
    }

    /**
     * Retrieve prepared field list
     *
     * @return array
     */
    public function getFieldList()
    {
        return $this->_fieldList;
    }

    /**
     * Parse row fields
     *
     * @param array $row
     */
    public function parseFieldList($row)
    {
        foreach (array_keys($row) as $fieldName) {
            if (!in_array($fieldName, $this->_fieldList)) {
                $this->_fieldList[$fieldName] = $fieldName;
            }
        }

        unset($fieldName, $row);
    }

    /**
     * Retrieve Io Adapter
     *
     * @return Mage_Dataflow_Model_Batch_Io
     */
    public function getIoAdapter()
    {
        if (is_null($this->_ioAdapter)) {
            $this->_ioAdapter = Mage::getModel('dataflow/batch_io');
            $this->_ioAdapter->init($this);
        }

        return $this->_ioAdapter;
    }

    protected function _beforeSave()
    {
        if (is_null($this->getData('created_at'))) {
            $this->setData('created_at', Mage::getSingleton('core/date')->gmtDate());
        }

        return $this;
    }

    protected function _afterDelete()
    {
        $this->getIoAdapter()->clear();
        return $this;
    }

    /**
     * Retrieve Batch export model
     *
     * @return Mage_Dataflow_Model_Batch_Export
     * @throws Mage_Core_Exception
     * @throws Varien_Exception
     */
    public function getBatchExportModel()
    {
        if (is_null($this->_batchExport)) {
            $object = Mage::getModel('dataflow/batch_export');
            $object->setBatchId($this->getId());
            /** @var Mage_Dataflow_Model_Batch_Export $cache */
            $cache = Varien_Object_Cache::singleton()->save($object);
            $this->_batchExport = $cache;
        }

        /** @var Mage_Dataflow_Model_Batch_Export $cache */
        $cache = Varien_Object_Cache::singleton()->load($this->_batchExport);
        return $cache;
    }

    /**
     * Retrieve Batch import model
     *
     * @return Mage_Dataflow_Model_Batch_Import
     * @throws Mage_Core_Exception
     * @throws Varien_Exception
     */
    public function getBatchImportModel()
    {
        if (is_null($this->_batchImport)) {
            $object = Mage::getModel('dataflow/batch_import');
            $object->setBatchId($this->getId());
            /** @var Mage_Dataflow_Model_Batch_Import $cache */
            $cache = Varien_Object_Cache::singleton()->save($object);
            $this->_batchImport = $cache;
        }

        /** @var Mage_Dataflow_Model_Batch_Import $cache */
        $cache = Varien_Object_Cache::singleton()->load($this->_batchImport);
        return $cache;
    }

    /**
     * Run finish actions for Adapter
     */
    public function beforeFinish()
    {
        if ($this->getAdapter()) {
            $adapter = Mage::getModel($this->getAdapter());
            if (method_exists($adapter, 'finish')) {
                $adapter->finish();
            }
        }
    }

    /**
     * Set additional params
     * automatic convert to serialize data
     *
     * @param  mixed $data
     * @return $this
     */
    public function setParams($data)
    {
        $this->setData('params', serialize($data));
        return $this;
    }

    /**
     * Retrieve additional params
     * return unserialize data
     *
     * @return mixed
     */
    public function getParams()
    {
        $data = $this->_data['params'];
        return unserialize($data, ['allowed_classes' => false]);
    }
}
