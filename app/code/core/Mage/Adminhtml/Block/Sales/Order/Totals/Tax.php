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
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml order tax totals block
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Sales_Order_Totals_Tax extends Mage_Tax_Block_Sales_Order_Tax
{
    /**
     * Get full information about taxes applied to order
     *
     * @return array
     */
    public function getFullTaxInfo()
    {
        $source = $this->getSource();
        $info = array();
        if ($source instanceof Mage_Sales_Model_Order) {
            $rates = Mage::getModel('sales/order_tax')->getCollection()->loadByOrder($source)->toArray();
            $info  = Mage::getSingleton('tax/calculation')->reproduceProcess($rates['items']);
        }
        
        return $info;
    }

    /**
     * Display tax abount
     *
     * @return string
     */
    public function displayAmount($amount, $baseAmount)
    {
        return Mage::helper('adminhtml/sales')->displayPrices($this->getSource(), $baseAmount, $amount, false, '<br />');
    }

    /**
     * Get store object for process configuration settings
     */
    public function getStore()
    {
        return Mage::app()->getStore();
    }
}
