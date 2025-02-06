<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Tax
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
