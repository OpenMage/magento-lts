<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * Links block
 *
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
