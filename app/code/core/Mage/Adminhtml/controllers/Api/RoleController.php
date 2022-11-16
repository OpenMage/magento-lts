<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml roles controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Api_RoleController extends Mage_Adminhtml_Controller_Action
{
    /**
     * ACL resource
     * @see Mage_Adminhtml_Controller_Action::_isAllowed()
     */
    public const ADMIN_RESOURCE = 'system/api/roles';

    /**
     * Controller pre-dispatch method
     *
     * @return Mage_Adminhtml_Controller_Action
     */
    public function preDispatch()
    {
        $this->_setForcedFormKeyActions(['delete', 'save']);
        return parent::preDispatch();
    }

    protected function _initAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('system/services/roles');
        $this->_addBreadcrumb($this->__('Web services'), $this->__('Web services'));
        $this->_addBreadcrumb($this->__('Permissions'), $this->__('Permissions'));
        $this->_addBreadcrumb($this->__('Roles'), $this->__('Roles'));
        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('System'))
             ->_title($this->__('Web Services'))
             ->_title($this->__('Roles'));

        $this->_initAction();

        $this->_addContent($this->getLayout()->createBlock('adminhtml/api_roles'));

        $this->renderLayout();
    }

    public function roleGridAction()
    {
        $this->getResponse()
            ->setBody($this->getLayout()
            ->createBlock('adminhtml/api_grid_role')
            ->toHtml());
    }

    public function editRoleAction()
    {
        $this->_title($this->__('System'))
             ->_title($this->__('Web Services'))
             ->_title($this->__('Roles'));

        $this->_initAction();

        $roleId = $this->getRequest()->getParam('rid');
        if (intval($roleId) > 0) {
            $breadCrumb = $this->__('Edit Role');
            $breadCrumbTitle = $this->__('Edit Role');
            $this->_title($this->__('Edit Role'));
        } else {
            $breadCrumb = $this->__('Add New Role');
            $breadCrumbTitle = $this->__('Add New Role');
            $this->_title($this->__('New Role'));
        }
        $this->_addBreadcrumb($breadCrumb, $breadCrumbTitle);

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this->_addLeft(
            $this->getLayout()->createBlock('adminhtml/api_editroles')
        );
        $resources = Mage::getModel('api/roles')->getResourcesList();
        $this->_addContent(
            $this->getLayout()->createBlock('adminhtml/api_buttons')
                ->setRoleId($roleId)
                ->setRoleInfo(Mage::getModel('api/roles')->load($roleId))
                ->setTemplate('api/roleinfo.phtml')
        );
        $this->_addJs(
            $this->getLayout()->createBlock('adminhtml/template')->setTemplate('api/role_users_grid_js.phtml')
        );
        $this->renderLayout();
    }

    public function deleteAction()
    {
        $rid = $this->getRequest()->getParam('role_id', false);

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
            Mage::getModel("api/roles")->load($rid)->delete();
            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The role has been deleted.'));
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('An error occurred while deleting this role.'));
        }

        $this->_redirect("*/*/");
    }

    public function saveRoleAction()
    {
        $rid        = $this->getRequest()->getParam('role_id', false);
        $role = Mage::getModel('api/roles')->load($rid);
        if (!$role->getId() && $rid) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('This Role no longer exists'));
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

        $resource   = explode(',', $this->getRequest()->getParam('resource', false));
        $roleUsers  = $this->getRequest()->getParam('in_role_user', null);
        parse_str($roleUsers, $roleUsers);
        $roleUsers = array_keys($roleUsers);

        $oldRoleUsers = $this->getRequest()->getParam('in_role_user_old');
        parse_str($oldRoleUsers, $oldRoleUsers);
        $oldRoleUsers = array_keys($oldRoleUsers);

        $isAll = $this->getRequest()->getParam('all');
        if ($isAll) {
            $resource = ["all"];
        }

        try {
            $role = $role
                    ->setName($this->getRequest()->getParam('rolename', false))
                    ->setPid($this->getRequest()->getParam('parent_id', false))
                    ->setRoleType('G')
                    ->save();

            Mage::getModel("api/rules")
                ->setRoleId($role->getId())
                ->setResources($resource)
                ->saveRel();

            foreach ($oldRoleUsers as $oUid) {
                $this->_deleteUserFromRole($oUid, $role->getId());
            }

            foreach ($roleUsers as $nRuid) {
                $this->_addUserToRole($nRuid, $role->getId());
            }

            $rid = $role->getId();
            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The role has been saved.'));
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('An error occurred while saving this role.'));
        }

        $this->_redirect('*/*/editrole', ['rid' => $rid]);
    }

    public function editrolegridAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/api_role_grid_user')->toHtml());
    }

    protected function _deleteUserFromRole($userId, $roleId)
    {
        try {
            Mage::getModel("api/user")
                ->setRoleId($roleId)
                ->setUserId($userId)
                ->deleteFromRole();
        } catch (Exception $e) {
            throw $e;
            return false;
        }
        return true;
    }

    protected function _addUserToRole($userId, $roleId)
    {
        $user = Mage::getModel("api/user")->load($userId);
        $user->setRoleId($roleId)->setUserId($userId);

        if ($user->roleUserExists() === true) {
            return false;
        } else {
            $user->add();
            return true;
        }
    }
}
