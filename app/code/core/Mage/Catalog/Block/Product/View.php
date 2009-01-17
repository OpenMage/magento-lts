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


/**
 * Product View block
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @module     Catalog
 */
class Mage_Catalog_Block_Product_View extends Mage_Catalog_Block_Product_Abstract
{
    protected function _prepareLayout()
    {
        $this->getLayout()->createBlock('catalog/breadcrumbs');
        if ($headBlock = $this->getLayout()->getBlock('head')) {
            if ($title = $this->getProduct()->getMetaTitle()) {
                $headBlock->setTitle($title);
            }

            if ($keyword = $this->getProduct()->getMetaKeyword()) {
                $headBlock->setKeywords($keyword);
            } elseif( $currentCategory = Mage::registry('current_category') ) {
                $headBlock->setKeywords($this->getProduct()->getName());
            }

            if ($description = $this->getProduct()->getMetaDescription()) {
                $headBlock->setDescription( ($description) );
            } else {
                $headBlock->setDescription( $this->getProduct()->getDescription() );
            }
        }
        return parent::_prepareLayout();
    }

    /**
     * Retrieve current product model
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        if (!Mage::registry('product') && $this->getProductId()) {
            $product = Mage::getModel('catalog/product')->load($this->getProductId());
            Mage::register('product', $product);
        }
        return Mage::registry('product');
    }

    public function canEmailToFriend()
    {
        $sendToFriendModel = Mage::registry('send_to_friend_model');
        return $sendToFriendModel && $sendToFriendModel->canEmailToFriend();
    }

    public function getAddToCartUrl($product, $additional = array())
    {
        $additional = array();

        if ($this->getRequest()->getParam('wishlist_next')){
            $additional['wishlist_next'] = 1;
        }

        return parent::getAddToCartUrl($product, $additional);
    }

    public function getJsonConfig()
    {
        $config = array();

        $_request = Mage::getSingleton('tax/calculation')->getRateRequest(false, false, false);
        $_request->setProductClassId($this->getProduct()->getTaxClassId());
        $defaultTax = Mage::getSingleton('tax/calculation')->getRate($_request);

        $_request = Mage::getSingleton('tax/calculation')->getRateRequest();
        $_request->setProductClassId($this->getProduct()->getTaxClassId());
        $currentTax = Mage::getSingleton('tax/calculation')->getRate($_request);

        $_regularPrice = $this->getProduct()->getPrice();
        $_finalPrice = $this->getProduct()->getFinalPrice();
        $_priceInclTax = Mage::helper('tax')->getPrice($this->getProduct(), $_finalPrice, true);
        $_priceExclTax = Mage::helper('tax')->getPrice($this->getProduct(), $_finalPrice);

        $idSuffix = '__none__';
        if ($this->hasOptions()) {
            $idSuffix = '_clone';
        }

        $config = array(
            'productId'           => $this->getProduct()->getId(),
            'priceFormat'         => Mage::app()->getLocale()->getJsPriceFormat(),
            'includeTax'          => Mage::helper('tax')->priceIncludesTax() ? 'true' : 'false',
            'showIncludeTax'      => Mage::helper('tax')->displayPriceIncludingTax(),
            'showBothPrices'      => Mage::helper('tax')->displayBothPrices(),
            'productPrice'        => Mage::helper('core')->currency($_finalPrice, false, false),
            'productOldPrice'     => Mage::helper('core')->currency($_regularPrice, false, false),
            'skipCalculate'       => ($_priceExclTax != $_priceInclTax ? 0 : 1),
            'defaultTax'          => $defaultTax,
            'currentTax'          => $currentTax,
            'idSuffix'            => $idSuffix,
            'oldPlusDisposition'  => 0,
            'plusDisposition'     => 0,
            'oldMinusDisposition' => 0,
            'minusDisposition'    => 0,
        );

		$responseObject = new Varien_Object();
		Mage::dispatchEvent('catalog_product_view_config', array('response_object'=>$responseObject));
		if (is_array($responseObject->getAdditionalOptions())) {
			foreach ($responseObject->getAdditionalOptions() as $option=>$value) {
				$config[$option] = $value;
			}
		}

        return Zend_Json::encode($config);
    }

    /**
     * Return true if product has options
     *
     * @return bool
     */
    public function hasOptions()
    {
        if ($this->getProduct()->getTypeInstance()->hasOptions()) {
            return true;
        }
        return false;
    }

    /**
     * Check if product has required options
     *
     * @return bool
     */
    public function hasRequiredOptions()
    {
        return $this->getProduct()->getTypeInstance()->hasRequiredOptions();
    }
}
