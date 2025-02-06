<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Class Mage_Adminhtml_Block_Checkout_Formkey
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Checkout_Formkey extends Mage_Adminhtml_Block_Template
{
    /**
     * Check form key validation on checkout.
     * If disabled, show notice.
     *
     * @return bool
     */
    public function canShow()
    {
        return !Mage::getStoreConfigFlag('admin/security/validate_formkey_checkout');
    }

    /**
     * Get url for edit Advanced -> Admin section
     *
     * @return string
     */
    public function getSecurityAdminUrl()
    {
        return Mage::helper('adminhtml')->getUrl('adminhtml/system_config/edit/section/admin');
    }
}
