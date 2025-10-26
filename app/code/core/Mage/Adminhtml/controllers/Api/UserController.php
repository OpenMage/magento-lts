<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Api_UserController extends Mage_Adminhtml_Controller_Action
{
    /**
     * ACL resource
     * @see Mage_Adminhtml_Controller_Action::_isAllowed()
     */
    public const ADMIN_RESOURCE = 'system/api/users';

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
            ->_setActiveMenu('system/api/users')
            ->_addBreadcrumb($this->__('Web Services'), $this->__('Web Services'))
            ->_addBreadcrumb($this->__('Permissions'), $this->__('Permissions'))
            ->_addBreadcrumb($this->__('Users'), $this->__('Users'))
        ;
        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('System'))
             ->_title($this->__('Web Services'))
             ->_title($this->__('Users'));

        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('adminhtml/api_user'))
            ->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $this->_title($this->__('System'))
             ->_title($this->__('Web Services'))
             ->_title($this->__('Users'));

        $id = $this->getRequest()->getParam('user_id');
        $model = Mage::getModel('api/user');

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

        Mage::register('api_user', $model);

        $this->_initAction()
            ->_addBreadcrumb(
                $id ? $this->__('Edit User') : $this->__('New User'),
                $id ? $this->__('Edit User') : $this->__('New User'),
            )
            ->_addContent(
                $this->getLayout()->createBlock('adminhtml/api_user_edit')
                    ->setData('action', $this->getUrl('*/api_user/save')),
            )
            ->_addLeft($this->getLayout()->createBlock('adminhtml/api_user_edit_tabs'));

        $this->_addJs(
            $this->getLayout()->createBlock('adminhtml/template')->setTemplate('api/user_roles_grid_js.phtml'),
        );
        $this->renderLayout();
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $id = $this->getRequest()->getPost('user_id', false);
            $model = Mage::getModel('api/user')->load($id);
            if (!$model->getId() && $id) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('This user no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }

            //Validate current admin password
            $currentPassword = $this->getRequest()->getParam('current_password', null);
            $this->getRequest()->setParam('current_password', null);
            unset($data['current_password']);
            $result = $this->_validateCurrentPassword($currentPassword);
            $model->setData($data);

            if ($model->hasNewApiKey() && $model->getNewApiKey() === '') {
                $model->unsNewApiKey();
            }

            if ($model->hasApiKeyConfirmation() && $model->getApiKeyConfirmation() === '') {
                $model->unsApiKeyConfirmation();
            }

            if (!is_array($result)) {
                $result = $model->validate();
            }

            if (is_array($result)) {
                foreach ($result as $error) {
                    $this->_getSession()->addError($error);
                }

                if ($id) {
                    $this->_getSession()->setUserData($data);
                    $this->_redirect('*/*/edit', ['user_id' => $id]);
                } else {
                    $this->_getSession()->setUserData($data);
                    $this->_redirect('*/*/new');
                }

                return;
            }

            try {
                $model->save();
                if ($uRoles = $this->getRequest()->getParam('roles', false)) {
                    if (count($uRoles) === 1) {
                        $model->setRoleIds($uRoles)
                            ->setRoleUserId($model->getUserId())
                            ->saveRelations();
                    } elseif (count($uRoles) > 1) {
                        //@FIXME: stupid fix of previous multi-roles logic.
                        //@TODO:  make proper DB upgrade in the future revisions.
                        $rs = [];
                        $rs[0] = $uRoles[0];
                        $model->setRoleIds($rs)->setRoleUserId($model->getUserId())->saveRelations();
                    }
                }

                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The user has been saved.'));
                Mage::getSingleton('adminhtml/session')->setUserData(false);
                $this->_redirect('*/*/edit', ['user_id' => $model->getUserId()]);
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setUserData($data);
                $this->_redirect('*/*/edit', ['user_id' => $model->getUserId()]);
                return;
            }
        }

        $this->_redirect('*/*/');
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

        if ($id) {
            try {
                $model = Mage::getModel('api/user')->load($id);
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The user has been deleted.'));
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', ['user_id' => $id]);
                return;
            }
        }

        Mage::getSingleton('adminhtml/session')->addError($this->__('Unable to find a user to delete.'));
        $this->_redirect('*/*/');
    }

    public function rolesGridAction()
    {
        $id = $this->getRequest()->getParam('user_id');
        $model = Mage::getModel('api/user');

        if ($id) {
            $model->load($id);
        }

        Mage::register('api_user', $model);
        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/api_user_edit_tab_roles')->toHtml());
    }

    public function roleGridAction()
    {
        $this->getResponse()
            ->setBody($this->getLayout()
            ->createBlock('adminhtml/api_user_grid')
            ->toHtml());
    }
}
