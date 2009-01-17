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
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Catalog_Model_Observer
{
    public function storeEdit($observer)
    {
        $store = $observer->getEvent()->getStore();
        /* @var $store Mage_Core_Model_Store */
        if ($store->dataHasChangedFor('group_id')) {
            Mage::app()->reinitStores();
            Mage::getModel('catalog/url')->refreshRewrites($store->getId());
        }
        Mage::getResourceModel('catalog/product')->refreshEnabledIndex($store);
        return $this;
    }

    public function storeAdd($observer)
    {
        $store = $observer->getEvent()->getStore();
        /* @var $store Mage_Core_Model_Store */
        Mage::app()->reinitStores();
        Mage::getConfig()->reinit();
        Mage::getModel('catalog/url')->refreshRewrites($store->getId());
        Mage::getResourceModel('catalog/product')->refreshEnabledIndex($store);
        return $this;
    }

    public function storeGroupSave($observer)
    {
        $group = $observer->getEvent()->getGroup();
        /* @var $group Mage_Core_Model_Store_Group */
        if ($group->dataHasChangedFor('root_category_id')) {
            Mage::app()->reinitStores();
            foreach ($group->getStores() as $store) {
                Mage::getModel('catalog/url')->refreshRewrites($store->getId());
            }
        }
        return $this;
    }

    public function categoryMove($observer)
    {
        $categoryId = $observer->getEvent()->getCategoryId();
        $prevParentId = $observer->getEvent()->getPrevParentId();
        $parentId = $observer->getEvent()->getParentId();
        Mage::getModel('catalog/url')->refreshCategoryRewrite($categoryId);
        $model = Mage::getModel('catalog/category')->load($prevParentId)->save();
        $model = Mage::getModel('catalog/category')->load($parentId)->save();
        $model = null;
        return $this;
    }

    public function catalogProductImportAfter($observer)
    {
        Mage::getModel('catalog/url')->refreshRewrites();
        return $this;
    }

    /**
     * Catalog Product Compare Items Clean
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Catalog_Model_Observer
     */
    public function catalogProductCompareClean(Varien_Event_Observer $observer)
    {
        Mage::getModel('catalog/product_compare_item')->clean();
        return $this;
    }
}
