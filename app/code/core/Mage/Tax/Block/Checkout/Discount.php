<?php

/**
 * @category   Mage
 * @package    Mage_Tax
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Subtotal Total Row Renderer
 *
 * @category   Mage
 * @package    Mage_Tax
 */
class Mage_Tax_Block_Checkout_Discount extends Mage_Checkout_Block_Total_Default
{
    //protected $_template = 'tax/checkout/subtotal.phtml';

    /**
     * @return bool
     */
    public function displayBoth()
    {
        return Mage::getSingleton('tax/config')->displayCartSubtotalBoth();
    }
}
