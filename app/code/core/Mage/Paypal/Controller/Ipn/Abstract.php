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
 * Unified IPN controller for all supported PayPal methods
 */
abstract class Mage_Paypal_Controller_Ipn_Abstract extends Mage_Core_Controller_Front_Action
{
    /**
     * Config Model Type
     *
     * @var string
     */
    protected $_configType = 'paypal/config';

    /**
     * Process IPN for PayPal Standard
     */
    public function standardAction()
    {
        return $this->_ipnAction(Mage_Paypal_Model_Config::METHOD_WPS);
    }

    /**
     * Process IPN for PayPal Express
     */
    public function expressAction()
    {
        return $this->_ipnAction(Mage_Paypal_Model_Config::METHOD_WPP_EXPRESS);
    }

    /**
     * Process IPN for PayPal Direct
     */
    public function directAction()
    {
        return $this->_ipnAction(Mage_Paypal_Model_Config::METHOD_WPP_DIRECT);
    }

    /**
     * Actually process the IPN
     * @param string $method
     */
    protected function _ipnAction($method)
    {
        if (!$this->getRequest()->isPost()) {
            return;
        }
        $config = Mage::getModel($this->_configType, array($method));
        if (!$config->active) {
            return;
        }
        Mage::getModel('paypal/ipn')
            ->setConfig($config)
            ->setIpnFormData($this->getRequest()->getPost())
            ->processIpnRequest();
    }
}
