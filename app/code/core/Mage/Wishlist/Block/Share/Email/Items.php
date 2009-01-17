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
 * @package    Mage_Wishlist
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Wishlist block customer items
 *
 * @category   Mage
 * @package    Mage_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Wishlist_Block_Share_Email_Items extends Mage_Core_Block_Template
{

    protected $_wishlistLoaded = false;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('wishlist/email/items.phtml');
    }

    public function getWishlist()
    {
        if(!$this->_wishlistLoaded) {
            Mage::registry('wishlist')
                ->loadByCustomer(Mage::getSingleton('customer/session')->getCustomer());
            Mage::registry('wishlist')->getProductCollection()
                ->addAttributeToSelect('url_key')
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('price')
                ->addAttributeToSelect('image')
                ->addAttributeToSelect('small_image')
                //->addAttributeToFilter('store_id', array('in'=>Mage::registry('wishlist')->getSharedStoreIds()))
                ->addStoreFilter()
                ->load();

            $this->_wishlistLoaded = true;
        }

        return Mage::registry('wishlist')->getProductCollection();
    }

    public function getEscapedDescription(Varien_Object $item)
    {
        return nl2br($this->htmlEscape($item->getDescription()));
    }

    public function hasDescription(Varien_Object $item)
    {
        return trim($item->getDescription())!='';
    }

    public function getFormatedDate($date)
    {
        return $this->formatDate($date, Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
    }

}
