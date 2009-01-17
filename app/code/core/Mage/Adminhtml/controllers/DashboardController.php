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
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Dashboard admin controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_DashboardController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('dashboard');
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Dashboard'), Mage::helper('adminhtml')->__('Dashboard'));
        $this->_addContent($this->getLayout()->createBlock('adminhtml/dashboard', 'dashboard'));
        $this->renderLayout();
    }

    public function productsViewedAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/dashboard_tab_products_viewed')->toHtml());
    }

    public function customersNewestAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/dashboard_tab_customers_newest')->toHtml());
    }

    public function customersMostAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/dashboard_tab_customers_most')->toHtml());
    }

    public function ajaxBlockAction()
    {
        $output   = '';
        $blockTab = $this->getRequest()->getParam('block');
        if (in_array($blockTab, array('tab_orders', 'tab_amounts'))) {
            $output = $this->getLayout()->createBlock('adminhtml/dashboard_' . $blockTab)->toHtml();
        }
        $this->getResponse()->setBody($output);
        return;
    }

    public function tunnelAction()
    {
        $httpClient = new Varien_Http_Client();

        if ($ga = $this->getRequest()->getParam('ga')) {
            if ($params = unserialize(base64_decode(urldecode($ga)))) {
                $response = $httpClient->setUri(Mage_Adminhtml_Block_Dashboard_Graph::API_URL)
                        ->setParameterGet($params)
                        ->setConfig(array('timeout' => 15))
                        ->request('GET');

                $headers = $response->getHeaders();

                $this->getResponse()
                    ->setHeader('Content-type', $headers['Content-type'])
                    ->setBody($response->getBody());
            }
        }
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('dashboard');
    }
}