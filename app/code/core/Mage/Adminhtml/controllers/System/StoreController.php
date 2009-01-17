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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Store controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_System_StoreController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        return $this;
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('system/store');
        $this->_addBreadcrumb(Mage::helper('core')->__('Stores'), Mage::helper('core')->__('Stores'));
        $this->_addContent($this->getLayout()->createBlock('adminhtml/system_store_store'));
        $this->renderLayout();
    }

    public function newWebsiteAction()
    {
        Mage::register('store_type', 'website');
        $this->_forward('newStore');
    }

    public function newGroupAction()
    {
        Mage::register('store_type', 'group');
        $this->_forward('newStore');
    }

    public function newStoreAction()
    {
        if (!Mage::registry('store_type')) {
            Mage::register('store_type', 'store');
        }
        Mage::register('store_action', 'add');
        $this->_forward('editStore');
    }

    public function editWebsiteAction()
    {
        Mage::register('store_type', 'website');
        $this->_forward('editStore');
    }

    public function editGroupAction()
    {
        Mage::register('store_type', 'group');
        $this->_forward('editStore');
    }

    public function editStoreAction()
    {
        $session = Mage::getSingleton('adminhtml/session');
        if ($session->getPostData()) {
            Mage::register('store_post_data', $session->getPostData());
            $session->unsPostData();
        }
        if (!Mage::registry('store_type')) {
            Mage::register('store_type', 'store');
        }
        if (!Mage::registry('store_action')) {
            Mage::register('store_action', 'edit');
        }
        switch (Mage::registry('store_type')) {
            case 'website':
                $itemId     = $this->getRequest()->getParam('website_id', null);
                $model      = Mage::getModel('core/website')->load($itemId);
                $notExists  = Mage::helper('core')->__('Website not exists');
                $codeBase   = Mage::helper('core')->__('Before modifying the website code please make sure that it is not used in index.php');
                break;
            case 'group':
                $itemId     = $this->getRequest()->getParam('group_id', null);
                $model      = Mage::getModel('core/store_group')->load($itemId);
                $notExists  = Mage::helper('core')->__('Store not exists');
                $codeBase   = false;
                break;
            case 'store':
                $itemId     = $this->getRequest()->getParam('store_id', null);
                $model      = Mage::getModel('core/store')->load($itemId);
                $notExists  = Mage::helper('core')->__('Store View not exists');
                $codeBase   = Mage::helper('core')->__('Before modifying the store view code please make sure that it is not used in index.php');
                break;
        }

        if ($model->getId() || Mage::registry('store_action') == 'add') {
            Mage::register('store_data', $model);

            if (Mage::registry('store_action') == 'edit' && $codeBase) {
                Mage::getSingleton('adminhtml/session')->addNotice($codeBase);
            }

            $this->loadLayout();
            $this->_setActiveMenu('system/store');
            $this->_addContent($this->getLayout()
                ->createBlock('adminhtml/system_store_edit')
                ->setData('action', $this->getUrl('*/*/save')));

            $this->renderLayout();
        }
        else {
            $session->addError($notExists);
            $this->_redirect('*/*/');
        }
    }

    public function saveAction()
    {
        if ($this->getRequest()->isPost() && $postData = $this->getRequest()->getPost()) {
            if (empty($postData['store_type']) || empty($postData['store_action'])) {
                $this->_redirect('*/*/');
                return;
            }
            $session = Mage::getSingleton('adminhtml/session');
            /* @var $session Mage_Adminhtml_Model_Session */

            try {
                switch ($postData['store_type']) {
                    case 'website':
                        $websiteModel = Mage::getModel('core/website');
                        if ($postData['website']['website_id']) {
                            $websiteModel->load($postData['website']['website_id']);
                        }
                        $websiteModel->setData($postData['website']);
                        if ($postData['website']['website_id'] == '') {
                            $websiteModel->setId(null);
                        }

                        $websiteModel->save();
                        $session->addSuccess(Mage::helper('core')->__('Website was successfully saved'));
                        break;

                    case 'group':
                        $groupModel = Mage::getModel('core/store_group');
                        if ($postData['group']['group_id']) {
                            $groupModel->load($postData['group']['group_id']);
                        }
                        $groupModel->setData($postData['group']);
                        if ($postData['group']['group_id'] == '') {
                            $groupModel->setId(null);
                        }

                        $groupModel->save();

                        Mage::dispatchEvent('store_group_save', array('group' => $groupModel));

                        $session->addSuccess(Mage::helper('core')->__('Store was successfully saved'));
                        break;

                    case 'store':
                        $eventName = 'store_edit';
                        $storeModel = Mage::getModel('core/store');
                        if ($postData['store']['store_id']) {
                            $storeModel->load($postData['store']['store_id']);
                        }
                        $storeModel->setData($postData['store']);
                        if ($postData['store']['store_id'] == '') {
                            $storeModel->setId(null);
                            $eventName = 'store_add';
                        }
                        $groupModel = Mage::getModel('core/store_group')->load($storeModel->getGroupId());
                        $storeModel->setWebsiteId($groupModel->getWebsiteId());
                        $storeModel->save();

                        Mage::dispatchEvent($eventName, array('store'=>$storeModel));

                        $session->addSuccess(Mage::helper('core')->__('Store View was successfully saved'));
                        break;
                    default:
                        $this->_redirect('*/*/');
                        return;
                }
                $this->_redirect('*/*/');
                return;
            }
            catch (Mage_Core_Exception $e) {
                $session->addError($e->getMessage());
                $session->setPostData($postData);
            }
            catch (Exception $e) {
                $session->addException($e, Mage::helper('core')->__('Error while saving. Please try again later.'));
                $session->setPostData($postData);
            }
            $this->_redirectReferer();
            return;
        }
        $this->_redirect('*/*/');
    }

    public function deleteWebsiteAction()
    {
        $session = Mage::getSingleton('adminhtml/session');
        $itemId = $this->getRequest()->getParam('website_id', null);
        if (!$model = Mage::getModel('core/website')->load($itemId)) {
            $session->addError(Mage::helper('core')->__('Unable to proceed. Please, try again'));
            $this->_redirect('*/*/');
            return ;
        }
        if (!$model->isCanDelete()) {
            $session->addError(Mage::helper('core')->__('This Website cannot be deleted'));
            $this->_redirect('*/*/editWebsite/', array('website_id'=>$model->getId()));
            return ;
        }

        Mage::getSingleton('adminhtml/session')->addNotice(
            Mage::helper('core')->__('Deleting a website will not delete the information associated with the website (e.g. stores, categories, products, etc.), but the website will not be able to be restored. It is suggested that you create a database backup before deleting the website.')
        );

        $this->loadLayout();
        $this->_addBreadcrumb(Mage::helper('core')->__('Delete Website'), Mage::helper('core')->__('Delete Website'));
        $this->_addContent($this->getLayout()->createBlock('adminhtml/system_store_delete_website')->setModel($model));
        $this->renderLayout();
    }

    public function deleteGroupAction()
    {
        $session = Mage::getSingleton('adminhtml/session');
        $itemId = $this->getRequest()->getParam('group_id', null);
        if (!$model = Mage::getModel('core/store_group')->load($itemId)) {
            $session->addError(Mage::helper('core')->__('Unable to proceed. Please, try again'));
            $this->_redirect('*/*/');
            return ;
        }
        if (!$model->isCanDelete()) {
            $session->addError(Mage::helper('core')->__('This Store cannot be deleted'));
            $this->_redirect('*/*/editGroup/', array('group_id'=>$model->getId()));
            return ;
        }

        Mage::getSingleton('adminhtml/session')->addNotice(
            Mage::helper('core')->__('Deleting a store will not delete the information associated with the store (e.g. categories, products, etc.), but the store will not be able to be restored. It is suggested that you create a database backup before deleting the store.')
        );

        $this->loadLayout();
        $this->_addBreadcrumb(Mage::helper('core')->__('Delete Store'), Mage::helper('core')->__('Delete Store'));
        $this->_addContent($this->getLayout()->createBlock('adminhtml/system_store_delete_group')->setModel($model));
        $this->renderLayout();
    }

    public function deleteStoreAction()
    {
        $session = Mage::getSingleton('adminhtml/session');
        $itemId = $this->getRequest()->getParam('store_id', null);
        if (!$model = Mage::getModel('core/store')->load($itemId)) {
            $session->addError(Mage::helper('core')->__('Unable to proceed. Please, try again'));
            $this->_redirect('*/*/');
            return ;
        }
        if (!$model->isCanDelete()) {
            $session->addError(Mage::helper('core')->__('This Store View cannot be deleted'));
            $this->_redirect('*/*/editStore/', array('store_id'=>$model->getId()));
            return ;
        }

        Mage::getSingleton('adminhtml/session')->addNotice(
            Mage::helper('core')->__('Deleting a store view will not delete the information associated with the store view (e.g. categories, products, etc.), but the store view will not be able to be restored. It is suggested that you create a database backup before deleting the store view.')
        );

        $this->loadLayout();
        $this->_addBreadcrumb(Mage::helper('core')->__('Delete Store View'), Mage::helper('core')->__('Delete Store View'));
        $this->_addContent($this->getLayout()->createBlock('adminhtml/system_store_delete_store')->setModel($model));
        $this->renderLayout();
    }

    public function deleteWebsitePostAction()
    {
        $session = Mage::getSingleton('adminhtml/session');
        /* @var $session Mage_Adminhtml_Model_Session */
        $itemId = $this->getRequest()->getParam('website_id');

        if (!$model = Mage::getModel('core/website')->load($itemId)) {
            $session->addError(Mage::helper('core')->__('Unable to proceed. Please, try again'));
            $this->_redirect('*/*/');
            return ;
        }
        if (!$model->isCanDelete()) {
            $session->addError(Mage::helper('core')->__('This Website cannot be deleted.'));
            $this->_redirect('*/*/editWebsite/', array('website_id'=>$model->getId()));
            return ;
        }

        if ($this->getRequest()->getParam('create_backup')) {
            try {
                $backupDb = Mage::getModel('backup/db');
                $backup   = Mage::getModel('backup/backup')
                    ->setTime(time())
                    ->setType('db')
                    ->setPath(Mage::getBaseDir("var") . DS . "backups");

                $backupDb->createBackup($backup);
                $session->addSuccess(Mage::helper('core')->__('Database was successfuly backed up.'));
            }
            catch (Mage_Core_Exception $e) {
                $session->addError($e->getMessage());
                $this->_redirect('*/*/editWebsite', array('website_id'=>$itemId));
                return ;
            }
            catch (Exception $e) {
                $session->addException($e, Mage::helper('core')->__('Unable to create backup. Please, try again later.'));
                $this->_redirect('*/*/editWebsite', array('website_id'=>$itemId));
                return ;
            }
        }

        try {
            $model->delete();
            $session->addSuccess(Mage::helper('core')->__('Website was successfully deleted.'));
            $this->_redirect('*/*/');
            return ;
        }
        catch (Mage_Core_Exception $e) {
            $session->addError($e->getMessage());
        }
        catch (Exception $e) {
            $session->addException($e, Mage::helper('core')->__('Unable to delete Website. Please, try again later.'));
        }
        $this->_redirect('*/*/editWebsite', array('website_id'=>$itemId));
    }

    public function deleteGroupPostAction()
    {
        $session = Mage::getSingleton('adminhtml/session');
        /* @var $session Mage_Adminhtml_Model_Session */
        $itemId = $this->getRequest()->getParam('group_id');

        if (!$model = Mage::getModel('core/store_group')->load($itemId)) {
            $session->addError(Mage::helper('core')->__('Unable to proceed. Please, try again'));
            $this->_redirect('*/*/');
            return ;
        }
        if (!$model->isCanDelete()) {
            $session->addError(Mage::helper('core')->__('This Store cannot be deleted.'));
            $this->_redirect('*/*/editGroup/', array('group_id'=>$model->getId()));
            return ;
        }

        if ($this->getRequest()->getParam('create_backup')) {
            try {
                $backupDb = Mage::getModel('backup/db');
                $backup   = Mage::getModel('backup/backup')
                    ->setTime(time())
                    ->setType('db')
                    ->setPath(Mage::getBaseDir("var") . DS . "backups");

                $backupDb->createBackup($backup);
                $session->addSuccess(Mage::helper('core')->__('Database was successfuly backed up.'));
            }
            catch (Mage_Core_Exception $e) {
                $session->addError($e->getMessage());
                $this->_redirect('*/*/editGroup', array('group_id'=>$itemId));
                return ;
            }
            catch (Exception $e) {
                $session->addException($e, Mage::helper('core')->__('Unable to create backup. Please, try again later.'));
                $this->_redirect('*/*/editGroup', array('group_id'=>$itemId));
                return ;
            }
        }

        try {
            $model->delete();
            $session->addSuccess(Mage::helper('core')->__('Store was successfully deleted.'));
            $this->_redirect('*/*/');
            return ;
        }
        catch (Mage_Core_Exception $e) {
            $session->addError($e->getMessage());
        }
        catch (Exception $e) {
            $session->addException($e, Mage::helper('core')->__('Unable to delete Store. Please, try again later.'));
        }
        $this->_redirect('*/*/editGroup', array('group_id'=>$itemId));
    }

    public function deleteStorePostAction()
    {
        $session = Mage::getSingleton('adminhtml/session');
        /* @var $session Mage_Adminhtml_Model_Session */
        $itemId = $this->getRequest()->getParam('store_id');

        if (!$model = Mage::getModel('core/store')->load($itemId)) {
            $session->addError(Mage::helper('core')->__('Unable to proceed. Please, try again'));
            $this->_redirect('*/*/');
            return ;
        }
        if (!$model->isCanDelete()) {
            $session->addError(Mage::helper('core')->__('This Store View cannot be deleted.'));
            $this->_redirect('*/*/editStore/', array('store_id'=>$model->getId()));
            return ;
        }

        if ($this->getRequest()->getParam('create_backup')) {
            try {
                $backupDb = Mage::getModel('backup/db');
                $backup   = Mage::getModel('backup/backup')
                    ->setTime(time())
                    ->setType('db')
                    ->setPath(Mage::getBaseDir("var") . DS . "backups");

                $backupDb->createBackup($backup);
                $session->addSuccess(Mage::helper('core')->__('Database was successfuly backed up.'));
            }
            catch (Mage_Core_Exception $e) {
                $session->addError($e->getMessage());
                $this->_redirect('*/*/editStore', array('store_id'=>$itemId));
                return ;
            }
            catch (Exception $e) {
                $session->addException($e, Mage::helper('core')->__('Unable to create backup. Please, try again later.'));
                $this->_redirect('*/*/editStore', array('store_id'=>$itemId));
                return ;
            }
        }

        try {
            $model->delete();

            Mage::dispatchEvent('store_delete', array('store'=>$model));

            $session->addSuccess(Mage::helper('core')->__('Store View was successfully deleted.'));
            $this->_redirect('*/*/');
            return ;
        }
        catch (Mage_Core_Exception $e) {
            $session->addError($e->getMessage());
        }
        catch (Exception $e) {
            $session->addException($e, Mage::helper('core')->__('Unable to delete Store View. Please, try again later.'));
        }
        $this->_redirect('*/*/editStore', array('store_id'=>$itemId));
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/store');
    }

}
