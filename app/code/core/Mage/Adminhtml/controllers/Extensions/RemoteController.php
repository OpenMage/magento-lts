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
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

require_once 'Varien/Pear/Package.php';

/**
 * Extension controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Extensions_RemoteController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout();

        $this->_setActiveMenu('system/extensions/remote');

        $this->_addContent($this->getLayout()->createBlock('adminhtml/extensions_remote'));

        $this->renderLayout();
    }

    public function gridAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/extensions_remote_grid')->toHtml());
    }

    public function editAction()
    {
        $this->loadLayout();

        $pkg = str_replace('|', '/', $this->getRequest()->getParam('id'));
        $ext = Mage::getModel('adminhtml/extension')->loadRemote($pkg);
#echo "<pre>".print_r($ext->getData(),1)."</pre>";
        Mage::register('remote_extension', $ext);
        $this->_setActiveMenu('system/extensions/remote');

        $this->_addContent($this->getLayout()->createBlock('adminhtml/extensions_remote_edit'));
        $this->_addLeft($this->getLayout()->createBlock('adminhtml/extensions_remote_edit_tabs'));

        $this->renderLayout();
    }

    public function installAction()
    {
        $pkg = str_replace('|', '/', $this->getRequest()->getParam('id'));
        $params = array('comment'=>Mage::helper('adminhtml')->__("Downloading and installing $pkg, please wait...")."\r\n\r\n");
        if ($this->getRequest()->getParam('do')) {
            $params['command'] = 'install';
            $params['options'] = array('onlyreqdeps'=>1);
            $params['params'] = array($pkg);
        }
        $result = Varien_Pear::getInstance()->runHtmlConsole($params);
        if (!$result instanceof PEAR_Error) {
            Mage::app()->cleanCache();
        }
        Mage::app()->getFrontController()->getResponse()->clearAllHeaders();
    }

    public function upgradeAction()
    {
        $pkg = str_replace('|', '/', $this->getRequest()->getParam('id'));
        $params = array('comment'=>Mage::helper('adminhtml')->__("Upgrading $pkg, please wait...")."\r\n\r\n");
        if ($this->getRequest()->getParam('do')) {
            $params['command'] = 'upgrade';
            $params['options'] = array();
            $params['params'] = array($pkg);
        }
        $result = Varien_Pear::getInstance()->runHtmlConsole($params);
        if (!$result instanceof PEAR_Error) {
            Mage::app()->cleanCache();
        }
        Mage::app()->getFrontController()->getResponse()->clearAllHeaders();
    }

    public function massInstallAction()
    {
        $this->loadLayout();

        $this->_setActiveMenu('system/extensions');

        $this->_addContent($this->getLayout()->createBlock('adminhtml/extensions_mass_install')->initForm());

        $this->renderLayout();
    }

    public function massInstallRunAction()
    {
        $params = array('comment'=>Mage::helper('adminhtml')->__("Installing selected packages, please wait...")."\r\n\r\n");
        if ($this->getRequest()->getParam('do')) {
            $params['command'] = 'install';
            $params['options'] = array();
            $packages = array();
            foreach ($this->getRequest()->getPost('package') as $package) {
                $packages[] = str_replace('|', '/', $package);
            }
            $params['params'] = $packages;
        }
        $result = Varien_Pear::getInstance()->runHtmlConsole($params);
        if (!$result instanceof PEAR_Error) {
            Mage::app()->cleanCache();
        }
        Mage::app()->getFrontController()->getResponse()->clearAllHeaders();
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/extensions/remote');
    }
}
