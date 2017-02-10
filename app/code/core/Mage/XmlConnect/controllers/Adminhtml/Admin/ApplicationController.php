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
 * @package     Mage_XmlConnect
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * XmlConnect Adminhtml mobile controller
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Adminhtml_Admin_ApplicationController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Admin application settings action
     */
    public function indexAction()
    {
        try {
            $isActive = $this->getRequest()->getParam('is_active', false);
            if (false !== $isActive) {
                Mage::getSingleton('xmlconnect/configuration')->saveIsActiveAdminApp($isActive);
                $this->_getSession()->addSuccess($this->__('Configuration data have been saved'));
            }
            $this->loadLayout();
            $this->_setActiveMenu('xmlconnect/mobile_admin_app');
            $this->renderLayout();
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirect('*/*/');
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addException($e, $this->__('Can\'t load admin application settings.'));
            $this->_redirect('*/*/');
        }
    }

    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('xmlconnect/admin_connect');
    }
}
