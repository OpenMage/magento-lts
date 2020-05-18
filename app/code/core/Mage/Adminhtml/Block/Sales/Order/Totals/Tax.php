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
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
        /** @var $source Mage_Sales_Model_Order */
        $source = $this->getOrder();

        $taxClassAmount = array();
        if ($source instanceof Mage_Sales_Model_Order) {
            $taxClassAmount = $this->_getTaxHelper()->getCalculatedTaxes($source);
        }

        return $taxClassAmount;
    }

    /**
     * Return Mage_Tax_Helper_Data instance
     *
     * @return Mage_Tax_Helper_Data
     */
    protected function _getTaxHelper()
    {
        return Mage::helper('tax');
    }

    /**
     * Display tax amount
     *
     * @param float $amount
     * @param float $baseAmount
     * @return string
     */
    public function displayAmount($amount, $baseAmount)
    {
        return Mage::helper('adminhtml/sales')->displayPrices(
            $this->getSource(), $baseAmount, $amount, false, '<br />'
        );
    }

    /**
     * Get store object for process configuration settings
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        return Mage::app()->getStore();
    }
}
