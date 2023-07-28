<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2018-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Permissions_UserController extends Mage_Adminhtml_Controller_Action
{
    /**
     * ACL resource
     * @see Mage_Adminhtml_Controller_Action::_isAllowed()
     */
    public const ADMIN_RESOURCE = 'system/acl/users';

    /**
     * Controller pre-dispatch method
     *
     * @return Mage_Adminhtml_Controller_Action
     */
    public function preDispatch()
    {
        $this->_setForcedFormKeyActions('delete');
        return parent::preDispatch();
    }

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('system/acl')
            ->_addBreadcrumb($this->__('System'), $this->__('System'))
            ->_addBreadcrumb($this->__('Permissions'), $this->__('Permissions'))
            ->_addBreadcrumb($this->__('Users'), $this->__('Users'))
        ;
        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('System'))
             ->_title($this->__('Permissions'))
             ->_title($this->__('Users'));

        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('adminhtml/permissions_user'))
            ->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $this->_title($this->__('System'))
             ->_title($this->__('Permissions'))
             ->_title($this->__('Users'));

        $id = $this->getRequest()->getParam('user_id');
        $model = Mage::getModel('admin/user');

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('This user no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $this->_title($model->getId() ? $model->getName() : $this->__('New User'));

        // Restore previously entered form data from session
        $data = Mage::getSingleton('adminhtml/session')->getUserData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register('permissions_user', $model);

        if (isset($id)) {
            $breadcrumb = $this->__('Edit User');
        } else {
            $breadcrumb = $this->__('New User');
        }
        $this->_initAction()
            ->_addBreadcrumb($breadcrumb, $breadcrumb);

        $this->getLayout()->getBlock('adminhtml.permissions.user.edit')
            ->setData('action', $this->getUrl('*/permissions_user/save'));

        $this->renderLayout();
    }

    public function saveAction()
    {
        $data = $this->getRequest()->getPost();

        if (!$data) {
            $this->_redirect('*/*/');
            return;
        }

        $id = $this->getRequest()->getParam('user_id');
        $role = $this->getRequest()->getParam('role');

        $user = Mage::getModel('admin/user')->load($id);
        $isNew = $user->isObjectNew();

        if ($id && !$user->getId()) {
            $this->_getSession()->addError($this->__('This user no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }

        $currentPassword = $this->getRequest()->getParam('current_password');
        $this->getRequest()->setParam('current_password', null);
        unset($data['current_password']);
        $result = $this->_validateCurrentPassword($currentPassword);

        $user->setData($data);

        /*
         * Unsetting new password and password confirmation if they are blank
         */
        if ($user->hasNewPassword() && $user->getNewPassword() === '') {
            $user->unsNewPassword();
        }
        if ($user->hasPasswordConfirmation() && $user->getPasswordConfirmation() === '') {
            $user->unsPasswordConfirmation();
        }

        if (!is_array($result)) {
            $result = $user->validate();
        }

        if (is_array($result)) {
            $this->_getSession()->setUserData($data);
            foreach ($result as $message) {
                $this->_getSession()->addError($message);
            }
            $this->_redirect('*/*/edit', ['_current' => true]);
            return;
        }

        try {
            $user->save();

            // Send notification to General and additional contacts (if declared) that a new admin user was created.
            if (Mage::getStoreConfigFlag('admin/security/crate_admin_user_notification') && $isNew) {
                Mage::getModel('admin/user')->sendAdminNotification($user);
            }

            if ($role) {
                $user->setRoleId((int)$role)
                    ->setRoleUserId($user->getUserId())
                    ->saveRelations();
            }
            $this->_getSession()->addSuccess($this->__('The user has been saved.'));
            $this->_getSession()->setUserData(false);
            $this->_redirect('*/*/');
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_getSession()->setUserData($data);
            $this->_redirect('*/*/edit', ['user_id' => $user->getUserId()]);
        }
    }

    public function deleteAction()
    {
        $id = $this->getRequest()->getParam('user_id');

        //Validate current admin password
        $currentPassword = $this->getRequest()->getParam('current_password', null);
        $this->getRequest()->setParam('current_password', null);
        $result = $this->_validateCurrentPassword($currentPassword);

        if (is_array($result)) {
            foreach ($result as $error) {
                $this->_getSession()->addError($error);
            }
            $this->_redirect('*/*/edit', ['user_id' => $id]);
            return;
        }

        $currentUser = Mage::getSingleton('admin/session')->getUser();

        if ($id = $this->getRequest()->getParam('user_id')) {
            if ($currentUser->getId() == $id) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('You cannot delete your own account.'));
                $this->_redirect('*/*/edit', ['user_id' => $id]);
                return;
            }
            try {
                $model = Mage::getModel('admin/user');
                $model->setId($id);
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The user has been deleted.'));
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', ['user_id' => $this->getRequest()->getParam('user_id')]);
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError($this->__('Unable to find a user to delete.'));
        $this->_redirect('*/*/');
    }

    public function rolesGridAction()
    {
        $id = $this->getRequest()->getParam('user_id');
        $model = Mage::getModel('admin/user');

        if ($id) {
            $model->load($id);
        }

        Mage::register('permissions_user', $model);
        $this->getResponse()->setBody(
            $this->getLayout()
                ->createBlock('adminhtml/permissions_user_edit_tab_roles')
                ->toHtml()
        );
    }

    public function roleGridAction()
    {
        $this->getResponse()
            ->setBody($this->getLayout()
            ->createBlock('adminhtml/permissions_user_grid')
            ->toHtml());
    }
}
