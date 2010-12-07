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

/**
 * file controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Extensions_FileController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout();

        $this->_setActiveMenu('system/extensions');

        $this->_addContent($this->getLayout()->createBlock('adminhtml/extensions_file_form')->initForm());

        $this->renderLayout();
    }

    public function installAction()
    {
        $params = array('comment'=>Mage::helper('adminhtml')->__("Pending installation...")."\r\n\r\n");
        if ($this->getRequest()->getParam('do')) {
            switch ($this->getRequest()->getParam('file_type')) {
                case 'local':
                    if (empty($_FILES['local']['tmp_name'])) {
                        $params['comment'] = Mage::helper('adminhtml')->__("Error uploading the file")."\r\n\r\n";
                        break;
                    }
                    $tmpDir = Mage::getBaseDir('var').DS.'pear';
                    if (!is_dir($tmpDir)) {
                        mkdir($tmpDir, 0777, true);
                    }
                    $pkg = $tmpDir.DS.$_FILES['local']['name'];
                    move_uploaded_file($_FILES['local']['tmp_name'], $pkg);

                    break;

                case 'remote':
                    $pkg = $this->getRequest()->getParam('remote');
                    if (empty($pkg)) {
                        $params['comment'] = Mage::helper('adminhtml')->__("Invalid URL")."\r\n\r\n";
                    }
                    break;
            }
            if (!empty($pkg)) {
                $params['comment'] = Mage::helper('adminhtml')->__("Installing $pkg, please wait...")."\r\n\r\n";
                $params['command'] = 'install';
                $params['options'] = array();
                $params['params'] = array($pkg);
            }
        }
        $result = Varien_Pear::getInstance()->runHtmlConsole($params);
        if (!$result instanceof PEAR_Error) {
            Mage::getModel('adminhtml/extension')->clearAllCache();
        }
    }

    public function upgradeAllAction()
    {
        $params = array('comment'=>Mage::helper('adminhtml')->__("Upgrading all packages, please wait...")."\r\n\r\n");
        if ($this->getRequest()->getParam('do')) {
            $params['command'] = 'upgrade';
            $params['options'] = array();
            $params['params'] = array($pkg);
        }
        $result = Varien_Pear::getInstance()->runHtmlConsole($params);
        if (!$result instanceof PEAR_Error) {
            Mage::app()->cleanCache();
        }
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/extensions');
    }
}
