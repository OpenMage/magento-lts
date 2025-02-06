<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Customer
 */

/**
 * Customer reset password form
 *
 * @category   Mage
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
