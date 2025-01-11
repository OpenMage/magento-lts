<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Checkout
 *
 * @method Mage_Checkout_Model_Resource_Agreement _getResource()
 * @method Mage_Checkout_Model_Resource_Agreement getResource()
 * @method Mage_Checkout_Model_Resource_Agreement_Collection getCollection()
 *
 * @method string getName()
 * @method $this setName(string $value)
 * @method string getContent()
 * @method $this setContent(string $value)
 * @method string getContentHeight()
 * @method $this setContentHeight(string $value)
 * @method string getCheckboxText()
 * @method $this setCheckboxText(string $value)
 * @method int getIsActive()
 * @method $this setIsActive(int $value)
 * @method int getIsHtml()
 * @method $this setIsHtml(int $value)
 * @method int getStoreId()
 */
class Mage_Checkout_Model_Agreement extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('checkout/agreement');
    }
}
