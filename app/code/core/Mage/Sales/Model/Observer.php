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
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Sales_Model_Observer
{
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
    }

    /**
     * When deleting product, substract it from all quotes quantities
     *
     * @throws Exception
     */
    public function substractQtyFromQuotes($observer)
    {
        $product = $observer->getEvent()->getProduct();
        Mage::getResourceSingleton('sales/quote')->substractProductFromQuotes($product);
    }

    /**
     * When applying a catalog price rule, make related quotes recollect on demand
     *
     * @param object $observer
     */
    public function markQuotesRecollectOnCatalogRules($observer)
    {
        Mage::getResourceSingleton('sales/quote')->markQuotesRecollectOnCatalogRules();
    }
}