<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Customer
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Class Mage_Customer_Block_Widget_Taxvat
 *
 * @category   Mage
 * @package    Mage_Customer
 *
 * @method $this setFieldIdFormat(string $value)
 * @method $this setFieldNameFormat(string $value)
 * @method $this setTaxvat(string $value)
 */
class Mage_Customer_Block_Widget_Taxvat extends Mage_Customer_Block_Widget_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('customer/widget/taxvat.phtml');
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return (bool)$this->_getAttribute('taxvat')->getIsVisible();
    }

    /**
     * @return bool
     */
    public function isRequired()
    {
        return (bool)$this->_getAttribute('taxvat')->getIsRequired();
    }

    /**
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        return Mage::getSingleton('customer/session')->getCustomer();
    }
}
