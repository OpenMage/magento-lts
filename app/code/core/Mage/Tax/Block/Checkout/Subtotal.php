<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Tax
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Subtotal Total Row Renderer
 *
 * @category   Mage
 * @package    Mage_Tax
 */
class Mage_Tax_Block_Checkout_Subtotal extends Mage_Checkout_Block_Total_Default
{
    /**
     *  Template for the block
     *
     * @var string
     */
    protected $_template = 'tax/checkout/subtotal.phtml';

    /**
     * The factory instance to get helper
     *
     * @var Mage_Core_Model_Factory
     *
     */
    protected $_factory;

    /**
     * Initialize factory instance
     */
    public function __construct(array $args = [])
    {
        $this->_factory = !empty($args['factory']) ? $args['factory'] : Mage::getSingleton('core/factory');
    }

    /**
     * @return bool
     */
    public function displayBoth()
    {
        return Mage::getSingleton('tax/config')->displayCartSubtotalBoth($this->getStore());
    }
}
