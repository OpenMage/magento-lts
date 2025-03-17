<?php

/**
 * This file is part of OpenMage.
For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */
class Mage_Customer_Block_Account_Dashboard_Hello extends Mage_Core_Block_Template
{
    /**
     * @return string
     */
    public function getCustomerName()
    {
        return Mage::getSingleton('customer/session')->getCustomer()->getName();
    }
}
