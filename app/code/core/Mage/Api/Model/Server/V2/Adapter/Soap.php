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
 * @category   Mage
 * @package    Mage_Api
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Webservice soap adapter
 *
 * @category   Mage
 * @package    Mage_Api
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api_Model_Server_V2_Adapter_Soap extends Mage_Api_Model_Server_Adapter_Soap
{
    /**
     * Run webservice
     *
     * @param Mage_Api_Controller_Action $controller
     * @return Mage_Api_Model_Server_Adapter_Soap
     */
    public function run()
    {
        $urlModel = Mage::getModel('core/url')
            ->setUseSession(false);
        if ($this->getController()->getRequest()->getParam('wsdl')) {
            $wsdlConfig = Mage::getModel('api/wsdl_config');
            $wsdlConfig->setHandler($this->getHandler())
                ->init();
            $this->getController()->getResponse()
                ->setHeader('Content-Type','text/xml')
                ->setBody($wsdlConfig->getWsdlContent());
        } elseif ($this->_extensionLoaded()) {
            $this->_soap = new SoapServer($urlModel->getUrl('*/*/*', array('wsdl'=>1)));
            use_soap_error_handler(false);
            $this->_soap->setClass($this->getHandler());
            $this->getController()->getResponse()
                ->setHeader('Content-Type', 'text/xml')
                ->setBody($this->_soap->handle());

        } else {
            $this->fault('0', 'Unable to load Soap extension on the server');
        }
        return $this;
    }


}
