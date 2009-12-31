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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Paypal Express Onepage checkout block
 *
 * @category   Mage
 * @package    Mage_Paypal
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Paypal_Block_Express_Review_Details extends Mage_Core_Block_Template
{
    protected $_address;

    /**
     * Get PayPal Express Review Information
     *
     * @return Mage_Paypal_Model_Express_Review
     */
    public function getReview()
    {
        return Mage::getSingleton('paypal/express_review');
    }

    public function getAddress()
    {
        if (empty($this->_address)) {
            $this->_address = $this->getReview()->getQuote()->getShippingAddress();
        }
        return $this->_address;
    }

    public function getItems()
    {
//		$priceFilter = Mage::app()->getStore()->getPriceFilter();
//        $itemsFilter = new Varien_Filter_Object_Grid();
//        $itemsFilter->addFilter(new Varien_Filter_Sprintf('%d'), 'qty');
//        $itemsFilter->addFilter($priceFilter, 'price');
//        $itemsFilter->addFilter($priceFilter, 'row_total');
//        return $itemsFilter->filter($this->getAddress()->getAllItems());
        return $this->getReview()->getQuote()->getAllItems();
    }

    public function getTotals()
    {
//        $totals = $this->getAddress()->getTotals();
//        $totalsFilter = new Varien_Filter_Object_Grid();
//        $totalsFilter->addFilter(Mage::app()->getStore()->getPriceFilter(), 'value');
//        return $totalsFilter->filter($totals);
        return $this->getReview()->getQuote()->getTotals();
    }
}