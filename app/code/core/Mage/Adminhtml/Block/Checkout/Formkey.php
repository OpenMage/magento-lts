<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Class Mage_Adminhtml_Block_Checkout_Formkey
 *
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
        return !Mage::helper('core')->isFormKeyEnabled();
    }

    /**
     * Get url for edit Advanced -> Admin section
     *
     * @return string
     * @deprecated
     */
    public function getSecurityAdminUrl()
    {
        return Mage::helper('adminhtml')::getUrl('adminhtml/system_config/edit/section/admin');
    }

    /**
     * @return string
     */
    public function getEnableCSRFUrl()
    {
        return Mage::helper("adminhtml")->getUrl('adminhtml/system_config/edit/section/system');
    }
}
