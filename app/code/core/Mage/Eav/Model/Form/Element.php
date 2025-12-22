<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Eav
 */

/**
 * Eav Form Element Model
 *
 * @package    Mage_Eav
 *
 * @method Mage_Eav_Model_Resource_Form_Element            _getResource()
 * @method int                                             getAttributeId()
 * @method Mage_Eav_Model_Resource_Form_Element_Collection getCollection()
 * @method int                                             getEntityTypeId()
 * @method int                                             getFieldsetId()
 * @method Mage_Eav_Model_Resource_Form_Element            getResource()
 * @method Mage_Eav_Model_Resource_Form_Element_Collection getResourceCollection()
 * @method int                                             getSortOrder()
 * @method int                                             getTypeId()
 * @method $this                                           setAttributeId(int $value)
 * @method $this                                           setFieldsetId(int $value)
 * @method $this                                           setSortOrder(int $value)
 * @method $this                                           setTypeId(int $value)
 */
class Mage_Eav_Model_Form_Element extends Mage_Core_Model_Abstract
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'eav_form_element';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('eav/form_element');
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

        if (!$this->getAttributeId()) {
            Mage::throwException(Mage::helper('eav')->__('Invalid EAV attribute.'));
        }

        return parent::_beforeSave();
    }

    /**
     * Retrieve EAV Attribute instance
     *
     * @return Mage_Eav_Model_Entity_Attribute
     */
    public function getAttribute()
    {
        if (!$this->hasData('attribute')) {
            $attribute = Mage::getSingleton('eav/config')
                ->getAttribute($this->getEntityTypeId(), $this->getAttributeId());
            $this->setData('attribute', $attribute);
        }

        return $this->_getData('attribute');
    }
}
