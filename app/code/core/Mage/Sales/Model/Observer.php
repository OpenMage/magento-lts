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
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Sales observer
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Observer
{
    /**
     * Clean expired quotes (cron process)
     *
     * @param Mage_Cron_Model_Schedule $schedule
     * @return Mage_Sales_Model_Observer
     */
    public function cleanExpiredQuotes($schedule)
    {
        $lifetimes = Mage::getConfig()->getStoresConfigByPath('checkout/cart/delete_quote_after');
        foreach ($lifetimes as $storeId=>$lifetime) {
            $lifetime *= 86400;

            $quotes = Mage::getModel('sales/quote')->getCollection();
            /* @var $quotes Mage_Sales_Model_Mysql4_Quote_Collection */

            $quotes->addFieldToFilter('store_id', $storeId);
            $quotes->addFieldToFilter('updated_at', array('to'=>date("Y-m-d", time()-$lifetime)));
            $quotes->addFieldToFilter('is_active', 0);
            $quotes->walk('delete');
        }
        return $this;
    }

    /**
     * When deleting product, substract it from all quotes quantities
     *
     * @throws Exception
     * @param Varien_Event_Observer
     * @return Mage_Sales_Model_Observer
     */
    public function substractQtyFromQuotes($observer)
    {
        $product = $observer->getEvent()->getProduct();
        Mage::getResourceSingleton('sales/quote')->substractProductFromQuotes($product);
        return $this;
    }

    /**
     * When applying a catalog price rule, make related quotes recollect on demand
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Sales_Model_Observer
     */
    public function markQuotesRecollectOnCatalogRules($observer)
    {
        Mage::getResourceSingleton('sales/quote')->markQuotesRecollectOnCatalogRules();
        return $this;
    }

    /**
     * Catalog Product After Save (change status process)
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Sales_Model_Observer
     */
    public function catalogProductSaveAfter(Varien_Event_Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();
        if ($product->getStatus() == Mage_Catalog_Model_Product_Status::STATUS_ENABLED) {
            return $this;
        }

        Mage::getResourceSingleton('sales/quote')->markQuotesRecollect($product->getId());

        return $this;
    }

    /**
     * Catalog Mass Status update process
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Sales_Model_Observer
     */
    public function catalogProductStatusUpdate(Varien_Event_Observer $observer)
    {
        $status     = $observer->getEvent()->getStatus();
        if ($status == Mage_Catalog_Model_Product_Status::STATUS_ENABLED) {
            return $this;
        }
        $productId  = $observer->getEvent()->getProductId();
        Mage::getResourceSingleton('sales/quote')->markQuotesRecollect($productId);

        return $this;
    }
}
