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
 * @package    Mage_CatalogRule
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_CatalogRule_Model_Rule extends Mage_Rule_Model_Rule
{
    protected $_productIds = array();

    protected $_now;

    protected function _construct()
    {
        parent::_construct();
        $this->_init('catalogrule/rule');
        $this->setIdFieldName('rule_id');
    }

    public function getConditionsInstance()
    {
        return Mage::getModel('catalogrule/rule_condition_combine');
    }

    public function getActionsInstance()
    {
        return Mage::getModel('catalogrule/rule_action_collection');
    }

    public function getNow()
    {
        if (!$this->_now) {
            return now();
        }
        return $this->_now;
    }

    public function setNow($now)
    {
        $this->_now = $now;
    }

    public function toString($format='')
    {
        $str = Mage::helper('catalogrule')->__("Name: %s", $this->getName()) ."\n"
             . Mage::helper('catalogrule')->__("Start at: %s", $this->getStartAt()) ."\n"
             . Mage::helper('catalogrule')->__("Expire at: %s", $this->getExpireAt()) ."\n"
             . Mage::helper('catalogrule')->__("Customer registered: %s", $this->getCustomerRegistered()) ."\n"
             . Mage::helper('catalogrule')->__("Customer is new buyer: %s", $this->getCustomerNewBuyer()) ."\n"
             . Mage::helper('catalogrule')->__("Description: %s", $this->getDescription()) ."\n\n"
             . $this->getConditions()->toStringRecursive() ."\n\n"
             . $this->getActions()->toStringRecursive() ."\n\n";
        return $str;
    }

    /**
     * Returns rule as an array for admin interface
     *
     * Output example:
     * array(
     *   'name'=>'Example rule',
     *   'conditions'=>{condition_combine::toArray}
     *   'actions'=>{action_collection::toArray}
     * )
     *
     * @return array
     */
    public function toArray(array $arrAttributes = array())
    {
        $out = parent::toArray($arrAttributes);
        $out['customer_registered'] = $this->getCustomerRegistered();
        $out['customer_new_buyer'] = $this->getCustomerNewBuyer();

        return $out;
    }
    /*
    public function processProduct(Mage_Catalog_Model_Product $product)
    {
        $this->validateProduct($product) && $this->updateProduct($product);
        return $this;
    }

    public function validateProduct(Mage_Catalog_Model_Product $product)
    {
        if (!$this->getIsCollectionValidated()) {
            $result = $result && $this->getIsActive()
                && (strtotime($this->getFromDate()) <= $this->getNow())
                && (strtotime($this->getToDate()) >= $this->getNow())
                && ($this->getCustomerRegistered()==2 || $this->getCustomerRegistered()==$env->getCustomerRegistered())
                && ($this->getCustomerNewBuyer()==2 || $this->getCustomerNewBuyer()==$env->getCustomerNewBuyer())
                && $this->getConditions()->validateProduct($product);
        } else {
            $result = $this->getConditions()->validateProduct($product);
        }

        return $result;
    }

    public function updateProduct(Mage_Sales_Model_Product $product)
    {
        $this->getActions()->updateProduct($product);
        return $this;
    }
    */
    public function getResourceCollection()
    {
        return Mage::getResourceModel('catalogrule/rule_collection');
    }

    protected function _afterSave()
    {
        $this->_getResource()->updateRuleProductData($this);
        parent::_afterSave();
    }

    public function getMatchingProductIds()
    {
        if (empty($this->_productIds)) {
            $productCollection = Mage::getResourceModel('catalog/product_collection');

            $this->setCollectedAttributes(array());
            $this->getConditions()->collectValidatedAttributes($productCollection);

            $this->_productIds = array();
            Mage::getSingleton('core/resource_iterator')->walk(
                $productCollection->getSelect(),
                array(array($this, 'callbackValidateProduct')),
                array(
                    'attributes'=>$this->getCollectedAttributes(),
                    'product'=>Mage::getModel('catalog/product'),
                )
            );
        }
        return $this->_productIds;
    }

    public function callbackValidateProduct($args)
    {
        $product = $args['product']->setData($args['row']);
        if (!empty($args['attributes']['category_ids'])) {
            $categoryCollection = $product->getCategoryCollection()->load();
            $categories = array();
            foreach ($categoryCollection as $category) {
                $categories[] = $category->getId();
            }
            $product->setCategoryIds($categories);
        }
        if ($this->getConditions()->validate($product)) {
            $this->_productIds[] = $product->getId();
        }
    }

    public function applyToProduct($product, $websiteIds=null)
    {
        if (is_numeric($product)) {
            $product = Mage::getModel('catalog/product')->load($product);
        }
        if (is_null($websiteIds)) {
            $websiteIds = explode(',', $this->getWebsiteIds());
        }
        $this->getResource()->applyToProduct($this, $product, $websiteIds);
    }

    public function getCustomerGroupIds()
    {
        $ids = $this->getData('customer_group_ids');
        if (($ids && !$this->getCustomerGroupChecked()) || is_string($ids)) {
            if (is_string($ids)) {
                $ids = explode(',', $ids);
            }

            $groupIds = Mage::getModel('customer/group')->getCollection()->getAllIds();
            $ids = array_intersect($ids, $groupIds);
            $this->setData('customer_group_ids', $ids);
            $this->setCustomerGroupChecked(true);
        }
        return $ids;
    }
}