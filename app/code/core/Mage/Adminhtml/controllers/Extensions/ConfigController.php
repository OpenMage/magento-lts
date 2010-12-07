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
 * config controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Extensions_ConfigController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout();

        $this->_setActiveMenu('system/extensions');

        $this->_addContent($this->getLayout()->createBlock('adminhtml/extensions_config_edit')->initForm());

        $this->renderLayout();
    }

    public function saveAction()
    {
        $pear = Varien_Pear::getInstance();
        $error = Mage::helper('adminhtml')->__("Unknown error");
        $state = $this->getRequest()->getPost('preferred_state');
        if (!empty($state)) {
            $session = Mage::getSingleton('adminhtml/session');
            $result = $pear->run('config-set', array(), array('preferred_state', $state));
            if ($result instanceof PEAR_Error) {
                $error = $result->getMessage();
            } else {
                $error = false;
            }
        }
        if ($error) {
            $session->addError($result->getMessage());
        } else {
            $session->addSuccess(Mage::helper('adminhtml')->__("PEAR Configuration was successfully saved"));
        }
        $this->_redirect('*/*');
    }
    
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/extensions/config');
    }
}
