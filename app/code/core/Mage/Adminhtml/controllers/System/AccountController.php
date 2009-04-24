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
 * Adminhtml account controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_System_AccountController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('system/account');
        $this->_addContent($this->getLayout()->createBlock('adminhtml/system_account_edit'));
        $this->renderLayout();
    }

    public function saveAction()
    {
        $userId = Mage::getSingleton('admin/session')->getUser()->getId();
        $pwd    = null;

        $user = Mage::getModel("admin/user")
                ->setId($userId)
                ->setUsername($this->getRequest()->getParam('username', false))
                ->setFirstname($this->getRequest()->getParam('firstname', false))
                ->setLastname($this->getRequest()->getParam('lastname', false))
                ->setEmail(strtolower($this->getRequest()->getParam('email', false)));
        if ( $this->getRequest()->getParam('password', false) ) {
            $user->setPassword($this->getRequest()->getParam('password', false));
        }

        try {
            try {
                $_isValid = Zend_Validate::is($user->getUsername(), 'NotEmpty')
                    && Zend_Validate::is($user->getFirstname(), 'NotEmpty')
                    && Zend_Validate::is($user->getLastname(), 'NotEmpty')
                    && Zend_Validate::is($user->getEmail(), 'EmailAddress');

                if (!$_isValid) {
                    Mage::throwException(Mage::helper('adminhtml')->__('Error while saving account. Please check all required fields'));
                }
                if ($user->userExists()) {
                    Mage::throwException(Mage::helper('adminhtml')->__('User with the same User Name or Email aleady exists'));
                }
                $user->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Account successfully saved'));
            }
            catch (Mage_Core_Exception $e) {
                throw $e;
            }
            catch (Exception $e) {
                throw new Exception(Mage::helper('adminhtml')->__('Error while saving account. Please try again later'));
            }
        }
        catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        $this->getResponse()->setRedirect($this->getUrl("*/*/"));
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/myaccount');
    }
}
