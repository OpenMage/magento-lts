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
 * @package     Mage_PaypalUk
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * UK domestic cards specific information block
 */
class Mage_PaypalUk_Block_Adminhtml_Direct_Iframe extends  Mage_Adminhtml_Block_Sales_Order_Create_Form
{
    /**
     * Set 3dsecure-specific parameters
     */
    public function __construct()
    {
        parent::__construct();
        $this->setJsObjectName('payPalUkCentinel');
        $this->setCentinelIframeId('paypaluk_3dsecure_iframe');
    }

    /**
     * Return 3D secure validate url
     *
     * @return string
     */
    public function getValidateUrl()
    {
        return $this->getUrl('*/paypaluk_direct/lookup', array('_current' => true, '_secure' => true));
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

    /**
     * Return direct paymethod
     *
     * @return Mage_PayPalUk_Model_Direct
     */
    public function getMethod()
    {
        return Mage::getSingleton('paypalUk/direct');
    }

}
