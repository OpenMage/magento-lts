<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Wishlist
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Links block
 *
 * @category   Mage
 * @package    Mage_Wishlist
 */
class Mage_Wishlist_Block_Links extends Mage_Page_Block_Template_Links_Block
{
    /**
     * Position in link list
     * @var int
     */
    protected $_position = 30;

    /**
     * @return string
     */
    protected function _toHtml()
    {
        /** @var Mage_Wishlist_Helper_Data $helper */
        $helper = $this->helper('wishlist');
        if ($helper->isAllow()) {
            $text = $this->_createLabel($this->_getItemCount());
            $this->_label = $text;
            $this->_title = $text;
            $this->_url = $this->getUrl('wishlist');
            return parent::_toHtml();
        }
        return '';
    }

    /**
     * Define label, title and url for wishlist link
     *
     * @deprecated after 1.6.2.0
     */
    public function initLinkProperties()
    {
        $text = $this->_createLabel($this->_getItemCount());
        $this->_label = $text;
        $this->_title = $text;
        $this->_url = $this->getUrl('wishlist');
    }

    /**
     * Count items in wishlist
     *
     * @return int
     */
    protected function _getItemCount()
    {
        /** @var Mage_Wishlist_Helper_Data $helper */
        $helper = $this->helper('wishlist');
        return $helper->getItemCount();
    }

    /**
     * Create button label based on wishlist item quantity
     *
     * @param int $count
     * @return string
     */
    protected function _createLabel($count)
    {
        if ($count > 1) {
            return $this->__('My Wishlist (%d items)', $count);
        }

        if ($count == 1) {
            return $this->__('My Wishlist (%d item)', $count);
        }

        return $this->__('My Wishlist');
    }

    /**
     * @return Mage_Wishlist_Block_Links
     * @see Mage_Wishlist_Block_Links::__construct
     *
     * @deprecated after 1.4.2.0
     */
    public function addWishlistLink()
    {
        return $this;
    }

    /**
     * Retrieve block cache tags
     *
     * @return array
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getCacheTags()
    {
        /** @var Mage_Wishlist_Helper_Data $helper */
        $helper = $this->helper('wishlist');
        $wishlist = $helper->getWishlist();
        $this->addModelTags($wishlist);
        foreach ($wishlist->getItemCollection() as $item) {
            $this->addModelTags($item);
        }
        return parent::getCacheTags();
    }
}
