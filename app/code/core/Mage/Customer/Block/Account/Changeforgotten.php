<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/**
 * Customer reset password form
 *
 * @package    Mage_Customer
 */
class Mage_Customer_Block_Account_Changeforgotten extends Mage_Core_Block_Template
{
    /**
     * Retrieve minimum length of customer password
     *
     * @return int
     */
    public function getMinPasswordLength()
    {
        return Mage::getModel('customer/customer')->getMinPasswordLength();
    }
}
