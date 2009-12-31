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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Dispamy PayPal Direct payment form
 */

class Mage_Paypal_Block_Direct_Form extends Mage_Payment_Block_Form_Cc
{
    /**
     * Init Form block, setup default js object names
     *
     * @return Mage_Paypal_Block_Direct
     */
    protected function _construct()
    {
        $this->setJsObjectName('payPalCentinel');
        $this->setCentinelIframeId('paypal_3dsecure_iframe');
        parent::_construct();
        return $this;
    }

    /**
     * Return 3D secure validate url
     *
     * @return string
     */
    public function getValidateUrl()
    {
        return $this->getUrl('paypal/direct/lookup', array('_secure' => true));
    }

    /**
     * Add UK domestic cards additional fields as child block
     *
     * Forks a clone, but with a different form
     *
     * @return Mage_PaypalUk_Block_Direct_Form
     */
    public function _beforeToHtml()
    {
        $child = clone $this;
        $this->setChild('paypal_direct',
            $child->setTemplate('paypal/direct/form.phtml'));
        return parent::_beforeToHtml();
    }

    /**
     * Return formated centinel js object name
     *
     * @return string
     */
    public function getCentinelJsObjectName()
    {
        return $this->getJsObjectName();
    }
}
