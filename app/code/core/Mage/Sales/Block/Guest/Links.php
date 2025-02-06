<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Sales
 */

/**
 * Links block
 *
 * @category   Mage
 * @package    Mage_Sales
 */
class Mage_Sales_Block_Guest_Links extends Mage_Page_Block_Template_Links_Block
{
    /**
     * Set link title, label and url
     */
    public function __construct()
    {
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            parent::__construct();

            $this->_label       = $this->__('Orders and Returns');
            $this->_title       = $this->__('Orders and Returns');
            $this->_url         = $this->getUrl('sales/guest/form');
        }
    }
}
