<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Dataflow
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Dataflow Batch model
 *
 * @category   Mage
 * @package    Mage_Dataflow
 *
 * @method Mage_Dataflow_Model_Resource_Batch _getResource()
 * @method Mage_Dataflow_Model_Resource_Batch getResource()
 * @method int getProfileId()
 * @method $this setProfileId(int $value)
 * @method int getStoreId()
 * @method $this setStoreId(int $value)
 * @method string getAdapter()
 * @method $this setAdapter(string $value)
 * @method string getCreatedAt()
 * @method $this setCreatedAt(string $value)
 */
class Mage_Dataflow_Model_Batch extends Mage_Core_Model_Abstract
{
    /**
     * Lifetime abandoned batches
     *
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
     * @var Mage_Dataflow_Model_Batch_Io|null
     */
    protected $_ioAdapter;

    /**
     * Dataflow batch export model
     *
     * @var Mage_Dataflow_Model_Batch_Export|null
     */
    protected $_batchExport;

    /**
     * Dataflow batch import model
     *
     * @var Mage_Dataflow_Model_Batch_Import|null
     */
    protected $_batchImport;

    /**
     * Init model
     *
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
        foreach ($row as $fieldName => $value) {
            if (!in_array($fieldName, $this->_fieldList)) {
                $this->_fieldList[$fieldName] = $fieldName;
            }
        }
        unset($fieldName, $value, $row);
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
     */
    public function getBatchExportModel()
    {
        if (is_null($this->_batchExport)) {
            $object = Mage::getModel('dataflow/batch_export');
            $object->setBatchId($this->getId());
            $this->_batchExport = Varien_Object_Cache::singleton()->save($object);
        }
        return Varien_Object_Cache::singleton()->load($this->_batchExport);
    }

    /**
     * Retrieve Batch import model
     *
     * @return Mage_Dataflow_Model_Batch_Import
     */
    public function getBatchImportModel()
    {
        if (is_null($this->_batchImport)) {
            $object = Mage::getModel('dataflow/batch_import');
            $object->setBatchId($this->getId());
            $this->_batchImport = Varien_Object_Cache::singleton()->save($object);
        }
        return Varien_Object_Cache::singleton()->load($this->_batchImport);
    }

    /**
     * Run finish actions for Adapter
     *
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
     * @param mixed $data
     * @return Mage_Dataflow_Model_Batch_Abstract
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
        $data = unserialize($data, ['allowed_classes' => false]);
        return $data;
    }
}
