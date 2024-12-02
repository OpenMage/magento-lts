<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Eav
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Eav Form Element Model
 *
 * @category   Mage
 * @package    Mage_Eav
 *
 * @method Mage_Eav_Model_Resource_Form_Element _getResource()
 * @method Mage_Eav_Model_Resource_Form_Element getResource()
 * @method Mage_Eav_Model_Resource_Form_Element_Collection getCollection()
 * @method int getTypeId()
 * @method $this setTypeId(int $value)
 * @method int getFieldsetId()
 * @method $this setFieldsetId(int $value)
 * @method int getAttributeId()
 * @method $this setAttributeId(int $value)
 * @method int getSortOrder()
 * @method $this setSortOrder(int $value)
 * @method int getEntityTypeId()
 */
class Mage_Eav_Model_Form_Element extends Mage_Core_Model_Abstract
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'eav_form_element';

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
