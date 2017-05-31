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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Class Mage_Adminhtml_Block_Checkout_Formkey
 */
class Mage_Adminhtml_Block_Checkout_Formkey extends Mage_Adminhtml_Block_Template
{
    /**
     * Check form key validation on checkout.
     * If disabled, show notice.
     *
     * @return boolean
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
        return Mage::helper("adminhtml")->getUrl('adminhtml/system_config/edit/section/admin');
    }
}
