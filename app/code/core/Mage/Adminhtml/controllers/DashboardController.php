<?php
/**
 * OpenMage
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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Dashboard admin controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_DashboardController extends Mage_Adminhtml_Controller_Action
{
    /**
     * ACL resource
     * @see Mage_Adminhtml_Controller_Action::_isAllowed()
     */
    const ADMIN_RESOURCE = 'dashboard';

    public function indexAction()
    {
        $this->_title($this->__('Dashboard'));

        $this->loadLayout();
        $this->_setActiveMenu('dashboard');
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Dashboard'), Mage::helper('adminhtml')->__('Dashboard'));
        $this->renderLayout();
    }

    /**
     * Gets most viewed products list
     */
    public function productsViewedAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Gets latest customers list
     */
    public function customersNewestAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Gets the list of most active customers
     */
    public function customersMostAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function ajaxBlockAction()
    {
        $output   = '';
        $blockTab = $this->getRequest()->getParam('block');
        if (in_array($blockTab, ['tab_orders', 'tab_amounts', 'totals'])) {
            $output = $this->getLayout()->createBlock('adminhtml/dashboard_' . $blockTab)->toHtml();
        }
        $this->getResponse()->setBody($output);
    }

    public function tunnelAction()
    {
        $httpClient = new Varien_Http_Client();
        $gaData = $this->getRequest()->getParam('ga');
        $gaHash = $this->getRequest()->getParam('h');
        if ($gaData && $gaHash) {
            $newHash = Mage::helper('adminhtml/dashboard_data')->getChartDataHash($gaData);
            if (hash_equals($newHash, $gaHash)) {
                $params = json_decode(base64_decode(urldecode($gaData)), true);
                if ($params) {
                    $response = $httpClient->setUri(Mage_Adminhtml_Block_Dashboard_Graph::API_URL)
                            ->setParameterGet($params)
                            ->setConfig(['timeout' => 5])
                            ->request('GET');

                    $headers = $response->getHeaders();

                    $this->getResponse()
                        ->setHeader('Content-type', $headers['Content-type'])
                        ->setBody($response->getBody());
                }
            }
        }
    }
}
