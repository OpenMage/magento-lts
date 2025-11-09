<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Wishlist
 */

/**
 * Wishlist block shared items
 *
 * @package    Mage_Wishlist
 */
class Mage_Wishlist_Block_Share_Wishlist extends Mage_Wishlist_Block_Abstract
{
    /**
     * Customer instance
     *
     * @var null|Mage_Customer_Model_Customer
     */
    protected $_customer = null;

    /**
     * Prepare global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle($this->getHeader());
        }

        return $this;
    }

    /**
     * Retrieve Shared Wishlist Customer instance
     *
     * @return Mage_Customer_Model_Customer
     */
    public function getWishlistCustomer()
    {
        if (is_null($this->_customer)) {
            $this->_customer = Mage::getModel('customer/customer')
                ->load($this->_getWishlist()->getCustomerId());
        }

        return $this->_customer;
    }

    /**
     * Retrieve Page Header
     *
     * @return string
     */
    public function getHeader()
    {
        return Mage::helper('wishlist')->__("%s's Wishlist", $this->escapeHtml($this->getWishlistCustomer()->getFirstname()));
    }
}
