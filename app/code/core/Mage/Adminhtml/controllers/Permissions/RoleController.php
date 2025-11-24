<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml roles controller
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Permissions_RoleController extends Mage_Adminhtml_Controller_Action
{
    /**
     * ACL resource
     * @see Mage_Adminhtml_Controller_Action::_isAllowed()
     */
    public const ADMIN_RESOURCE = 'system/acl/roles';

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

    /**
     * Preparing layout for output
     *
     * @return $this
     */
    protected function _initAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('system/acl/roles');
        $this->_addBreadcrumb($this->__('System'), $this->__('System'));
        $this->_addBreadcrumb($this->__('Permissions'), $this->__('Permissions'));
        $this->_addBreadcrumb($this->__('Roles'), $this->__('Roles'));
        return $this;
    }

    /**
     * Initialize role model by passed parameter in request
     *
     * @return Mage_Admin_Model_Roles
     */
    protected function _initRole($requestVariable = 'rid')
    {
        $this->_title($this->__('System'))
             ->_title($this->__('Permissions'))
             ->_title($this->__('Roles'));

        $role = Mage::getModel('admin/roles')->load($this->getRequest()->getParam($requestVariable));
        // preventing edit of relation role
        if ($role->getId() && $role->getRoleType() != 'G') {
            $role->unsetData($role->getIdFieldName());
        }

        Mage::register('current_role', $role);
        return Mage::registry('current_role');
    }

    /**
     * Show grid with roles existing in systems
     */
    public function indexAction()
    {
        $this->_title($this->__('System'))
             ->_title($this->__('Permissions'))
             ->_title($this->__('Roles'));

        $this->_initAction();
        $this->renderLayout();
    }

    /**
     * Action for ajax request from grid
     */
    public function roleGridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody($this->getLayout()->getBlock('adminhtml.permission.role.grid')->toHtml());
    }

    /**
     * Edit role action
     */
    public function editRoleAction()
    {
        $role = $this->_initRole();
        $this->_initAction();

        if ($role->getId()) {
            $breadCrumb      = $this->__('Edit Role');
            $breadCrumbTitle = $this->__('Edit Role');
        } else {
            $breadCrumb = $this->__('Add New Role');
            $breadCrumbTitle = $this->__('Add New Role');
        }

        $this->_title($role->getId() ? $role->getRoleName() : $this->__('New Role'));

        $this->_addBreadcrumb($breadCrumb, $breadCrumbTitle);

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this->_addContent(
            $this->getLayout()->createBlock('adminhtml/permissions_buttons')
                ->setRoleId($role->getId())
                ->setRoleInfo($role)
                ->setTemplate('permissions/roleinfo.phtml'),
        );
        $this->_addJs(
            $this->getLayout()->createBlock('adminhtml/template')->setTemplate('permissions/role_users_grid_js.phtml'),
        );
        $this->renderLayout();
    }

    /**
     * Remove role action
     */
    public function deleteAction()
    {
        $role = $this->_initRole('role_id');
        if (!$role->getId()) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('This Role no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }

        //Validate current admin password
        $currentPassword = $this->getRequest()->getParam('current_password', null);
        $this->getRequest()->setParam('current_password', null);
        $result = $this->_validateCurrentPassword($currentPassword);

        if (is_array($result)) {
            foreach ($result as $error) {
                $this->_getSession()->addError($error);
            }

            $this->_redirect('*/*/editrole', ['rid' => $role->getId()]);
            return;
        }

        $currentUser = Mage::getModel('admin/user')->setId(Mage::getSingleton('admin/session')->getUser()->getId());

        if (in_array($role->getId(), $currentUser->getRoles())) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Self-assigned roles cannot be deleted.'));
            $this->_redirect('*/*/editrole', ['rid' => $role->getId()]);
            return;
        }

        try {
            $role->delete();
            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The role has been deleted.'));
        } catch (Exception $exception) {
            Mage::logException($exception);
            Mage::getSingleton('adminhtml/session')->addError($this->__('An error occurred while deleting this role.'));
        }

        $this->_redirect('*/*/');
    }

    /**
     * Role form submit action to save or create new role
     */
    public function saveRoleAction()
    {
        $rid        = $this->getRequest()->getParam('role_id', false);
        $resource   = explode(',', $this->getRequest()->getParam('resource', false));
        $roleUsers  = $this->getRequest()->getParam('in_role_user', null);
        parse_str($roleUsers, $roleUsers);
        $roleUsers = array_keys($roleUsers);

        $oldRoleUsers = $this->getRequest()->getParam('in_role_user_old');
        parse_str($oldRoleUsers, $oldRoleUsers);
        $oldRoleUsers = array_keys($oldRoleUsers);

        $isAll = $this->getRequest()->getParam('all');
        if ($isAll) {
            $resource = ['all'];
        }

        $role = $this->_initRole('role_id');
        if (!$role->getId() && $rid) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('This Role no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }

        //Validate current admin password
        $currentPassword = $this->getRequest()->getParam('current_password', null);
        $this->getRequest()->setParam('current_password', null);
        $result = $this->_validateCurrentPassword($currentPassword);

        if (is_array($result)) {
            foreach ($result as $error) {
                $this->_getSession()->addError($error);
            }

            $this->_redirect('*/*/editrole', ['rid' => $rid]);
            return;
        }

        try {
            $roleName = $this->getRequest()->getParam('rolename', false);

            $role->setName($roleName)
                 ->setPid($this->getRequest()->getParam('parent_id', false))
                 ->setRoleType('G');
            Mage::dispatchEvent(
                'admin_permissions_role_prepare_save',
                ['object' => $role, 'request' => $this->getRequest()],
            );
            $role->save();

            Mage::getModel('admin/rules')
                ->setRoleId($role->getId())
                ->setResources($resource)
                ->saveRel();

            foreach ($oldRoleUsers as $oUid) {
                $this->_deleteUserFromRole($oUid, $role->getId());
            }

            foreach ($roleUsers as $nRuid) {
                $this->_addUserToRole($nRuid, $role->getId());
            }

            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The role has been successfully saved.'));
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::logException($e);
            Mage::getSingleton('adminhtml/session')->addError($this->__('An error occurred while saving this role.'));
        }

        $this->_redirect('*/*/');
    }

    /**
     * Action for ajax request from assigned users grid
     */
    public function editrolegridAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('adminhtml/permissions_role_grid_user')->toHtml(),
        );
    }

    /**
     * Remove user from role
     *
     * @param int $userId
     * @param int $roleId
     * @return true
     */
    protected function _deleteUserFromRole($userId, $roleId)
    {
        try {
            Mage::getModel('admin/user')
                ->setRoleId($roleId)
                ->setUserId($userId)
                ->deleteFromRole();
        } catch (Exception $exception) {
            throw $exception;
        }

        return true;
    }

    /**
     * Assign user to role
     *
     * @param int $userId
     * @param int $roleId
     * @return bool
     */
    protected function _addUserToRole($userId, $roleId)
    {
        $user = Mage::getModel('admin/user')->load($userId);
        $user->setRoleId($roleId)->setUserId($userId);

        if ($user->roleUserExists() === true) {
            return false;
        } else {
            $user->add();
            return true;
        }
    }

    /**
     * Action to refresh role-rule relations.
     * This method will make sure the rendered ACL resource tree checkboxes match the actual ACL permissions.
     * To be used after adding a new ACL resource via config
     */
    public function refreshRolesAction()
    {
        $resourceAcl = Mage::getResourceModel('admin/acl')->loadAcl();
        $roles = Mage::getResourceModel('admin/role_collection')->setRolesFilter()->getItems();
        try {
            foreach ($roles as $role) {
                $roleTypeId = $role->getRoleType() . $role->getRoleId();
                $selectedResourceIds = [];
                if ($resourceAcl->isAllowed($roleTypeId, 'all')) {
                    $selectedResourceIds = ['all'];
                } else {
                    foreach ($resourceAcl->getResources() as $resource) {
                        if ($resourceAcl->isAllowed($roleTypeId, $resource)) {
                            $selectedResourceIds[] = $resource;
                        }
                    }
                }

                Mage::getModel('admin/rules')
                    ->setRoleId($role->getId())
                    ->setResources($selectedResourceIds)
                    ->saveRel();
            }

            $users = Mage::getResourceModel('admin/user_collection');
            foreach ($users as $user) {
                $user->getResource()->saveReloadAclFlag($user, 1);
            }

            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The roles have been refreshed.'));
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::logException($e);
            Mage::getSingleton('adminhtml/session')->addError($this->__('An error occurred while refreshing roles.'));
        }

        $this->_redirect('*/*/');
    }
}
