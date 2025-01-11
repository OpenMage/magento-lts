<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Admin tax class content block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Tax_Class extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller      = 'tax_class';
        parent::__construct();
    }

    public function setClassType($classType)
    {
        if ($classType == Mage_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT) {
            $this->_headerText      = Mage::helper('tax')->__('Product Tax Classes');
            $this->_addButtonLabel  = Mage::helper('tax')->__('Add New Class');
        } elseif ($classType == Mage_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER) {
            $this->_headerText      = Mage::helper('tax')->__('Customer Tax Classes');
            $this->_addButtonLabel  = Mage::helper('tax')->__('Add New Class');
        }

        $this->getChild('grid')->setClassType($classType);
        $this->setData('class_type', $classType);

        return $this;
    }
}
