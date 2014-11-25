<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Tax
 * @copyright  Copyright (c) 2006-2014 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Tax Total Row Renderer
 *
 * @author Magento Core Team <core@magentocommerce.com>
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
     *
     * @param array $args
     */
    public function __construct(array $args = array())
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
        $allWeee = array();
        $store = $this->getTotal()->getAddress()->getQuote()->getStore();
        $helper = $this->_factory->getHelper('weee');

        if (!$helper->includeInSubtotal($store)) {
            foreach ($this->getTotal()->getAddress()->getCachedItemsAll() as $item) {
                foreach ($helper->getApplied($item) as $tax) {
                    $weeeDiscount = isset($tax['weee_discount']) ? $tax['weee_discount'] : 0;
                    $title = $tax['title'];
                    $rowAmount = isset($tax['row_amount']) ? $tax['row_amount'] : 0;
                    $rowAmountInclTax = isset($tax['row_amount_incl_tax']) ? $tax['row_amount_incl_tax'] : 0;
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
