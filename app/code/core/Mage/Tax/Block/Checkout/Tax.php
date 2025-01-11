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
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tax Total Row Renderer
 *
 */
class Mage_Tax_Block_Checkout_Tax extends Mage_Checkout_Block_Total_Default
{
    /**
     * Template used in the block
     *
     * @var string
     */
    protected $_template = 'tax/checkout/tax.phtml';

    /**
     * The factory instance to get helper
     *
     * @var Mage_Core_Model_Factory
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
     * Get all FPTs
     *
     * @return array
     */
    public function getAllWeee()
    {
        $allWeee = [];
        $store = $this->getTotal()->getAddress()->getQuote()->getStore();
        /** @var Mage_Weee_Helper_Data $helper */
        $helper = $this->_factory->getHelper('weee');

        if (!$helper->includeInSubtotal($store)) {
            foreach ($this->getTotal()->getAddress()->getCachedItemsAll() as $item) {
                foreach ($helper->getApplied($item) as $tax) {
                    $weeeDiscount = $tax['weee_discount'] ?? 0;
                    $title = $tax['title'];
                    $rowAmount = $tax['row_amount'] ?? 0;
                    $rowAmountInclTax = $tax['row_amount_incl_tax'] ?? 0;
                    $amountDisplayed = ($helper->isTaxIncluded()) ? $rowAmountInclTax : $rowAmount;
                    if (array_key_exists($title, $allWeee)) {
                        $allWeee[$title] = $allWeee[$title] + $amountDisplayed - $weeeDiscount;
                    } else {
                        $allWeee[$title] = $amountDisplayed - $weeeDiscount;
                    }
                }
            }
        }
        return $allWeee;
    }
}
