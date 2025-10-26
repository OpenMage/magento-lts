<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Eav
 */

/**
 * Eav Form Fieldset Model
 *
 * @package    Mage_Eav
 *
 * @method Mage_Eav_Model_Resource_Form_Fieldset _getResource()
 * @method Mage_Eav_Model_Resource_Form_Fieldset getResource()
 * @method Mage_Eav_Model_Resource_Form_Fieldset_Collection getCollection()
 * @method int getTypeId()
 * @method $this setTypeId(int $value)
 * @method string getCode()
 * @method $this setCode(string $value)
 * @method string getLabel()
 * @method bool hasLabels()
 * @method int getSortOrder()
 * @method $this setSortOrder(int $value)
 * @method bool hasStoreId()
 */
class Mage_Eav_Model_Form_Fieldset extends Mage_Core_Model_Abstract
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'eav_form_fieldset';

    protected function _construct()
    {
        $this->_init('eav/form_fieldset');
    }

    /**
     * Validate data before save data
     *
     * @throws Mage_Core_Exception
     * @inheritDoc
     */
    protected function _beforeSave()
    {
        if (!$this->getTypeId()) {
            Mage::throwException(Mage::helper('eav')->__('Invalid form type.'));
        }

        if (!$this->getStoreId() && $this->getLabel()) {
            $this->setStoreLabel($this->getStoreId(), $this->getLabel());
        }

        return parent::_beforeSave();
    }

    /**
     * Retrieve fieldset labels for stores
     *
     * @return array
     */
    public function getLabels()
    {
        if (!$this->hasData('labels')) {
            $this->setData('labels', $this->_getResource()->getLabels($this));
        }

        return $this->_getData('labels');
    }

    /**
     * Set fieldset store labels
     * Input array where key - store_id and value = label
     *
     * @return $this
     */
    public function setLabels(array $labels)
    {
        return $this->setData('labels', $labels);
    }

    /**
     * Set fieldset store label
     *
     * @param int $storeId
     * @param string $label
     * @return $this
     */
    public function setStoreLabel($storeId, $label)
    {
        $labels = $this->getLabels();
        $labels[$storeId] = $label;

        return $this->setLabels($labels);
    }

    /**
     * Retrieve label store scope
     *
     * @return int
     */
    public function getStoreId()
    {
        if (!$this->hasStoreId()) {
            $this->setData('store_id', Mage::app()->getStore()->getId());
        }

        return $this->_getData('store_id');
    }
}
