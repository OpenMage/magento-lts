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
 * @category   Mage
 * @package    Mage_Wishlist
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Wishlist block shared items
 *
 * @category   Mage
 * @package    Mage_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Wishlist_Block_Share_Wishlist extends Mage_Catalog_Block_Product_Abstract
{

    protected $_collection = null;
    protected $_customer = null;

    protected function _prepareLayout()
    {
        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->setTitle($this->getHeader());
        }
    }

    public function getWishlist()
    {
        if(is_null($this->_collection)) {
            $this->_collection = Mage::registry('shared_wishlist')->getProductCollection()
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('price')
                ->addAttributeToSelect('special_price')
                ->addAttributeToSelect('special_from_date')
                ->addAttributeToSelect('special_to_date')
                ->addAttributeToSelect('image')
                ->addAttributeToSelect('small_image')
                ->addAttributeToSelect('thumbnail')
                //->addAttributeToFilter('store_id', array('in'=>Mage::registry('shared_wishlist')->getSharedStoreIds()))
                ->addStoreFilter();

            Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($this->_collection);
            Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($this->_collection);
        }

        return $this->_collection;
    }

    public function getWishlistCustomer()
    {
        if(is_null($this->_customer)) {
            $this->_customer = Mage::getModel('customer/customer')
                ->load(Mage::registry('shared_wishlist')->getCustomerId());

        }

        return $this->_customer;
    }


    public function getEscapedDescription($item)
    {
        if ($item->getDescription()) {
            return $this->htmlEscape($item->getDescription());
        }
        return '&nbsp;';
    }

    public function getHeader()
    {
        return Mage::helper('wishlist')->__("%s's Wishlist", $this->htmlEscape($this->getWishlistCustomer()->getFirstname()));
    }

    public function getFormatedDate($date)
    {
        return $this->formatDate($date, Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
    }

    public function isSaleable()
    {
        foreach ($this->getWishlist() as $item) {
            if ($item->isSaleable()) {
                return true;
            }
        }

        return false;
    }
}
