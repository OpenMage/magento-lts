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
 * Convert Advanced admin controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_System_Convert_ProfileController extends Mage_Adminhtml_Controller_Action
{

    protected function _initProfile($idFieldName = 'id')
    {
        $profileId = (int) $this->getRequest()->getParam($idFieldName);
        $profile = Mage::getModel('dataflow/profile');

        if ($profileId) {
            $profile->load($profileId);
            if (!$profile->getId()) {
                Mage::getSingleton('adminhtml/session')->addError('The profile you are trying to save no longer exists');
                $this->_redirect('*/*');
                return false;
            }
        }

        Mage::register('current_convert_profile', $profile);

        return $this;
    }

    /**
     * Profiles list action
     */
    public function indexAction()
    {
        if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }
        $this->loadLayout();

        /**
         * Set active menu item
         */
        $this->_setActiveMenu('system/convert');

        /**
         * Append profiles block to content
         */
        $this->_addContent(
            $this->getLayout()->createBlock('adminhtml/system_convert_profile', 'convert_profile')
        );

        /**
         * Add breadcrumb item
         */
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Import/Export'), Mage::helper('adminhtml')->__('Import/Export Advanced'));
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Advanced Profiles'), Mage::helper('adminhtml')->__('Advanced Profiles'));

        $this->renderLayout();
    }

    public function gridAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/system_convert_profile_grid')->toHtml());
    }

    /**
     * Profile edit action
     */
    public function editAction()
    {
        $this->_initProfile();
        $this->loadLayout();

        $profile = Mage::registry('current_convert_profile');

        // set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getConvertProfileData(true);

        if (!empty($data)) {
            $profile->addData($data);
        }

        $this->_setActiveMenu('system/convert');

        $this->_addContent(
            $this->getLayout()->createBlock('adminhtml/system_convert_profile_edit')
        );

        /**
         * Append edit tabs to left block
         */
        $this->_addLeft($this->getLayout()->createBlock('adminhtml/system_convert_profile_edit_tabs'));

        $this->renderLayout();
    }

    /**
     * Create new profile action
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Delete profile action
     */
    public function deleteAction()
    {
        $this->_initProfile();
        $profile = Mage::registry('current_convert_profile');
        if ($profile->getId()) {
            try {
                $profile->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Profile was deleted'));
            }
            catch (Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*');
    }

    /**
     * Save profile action
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            if (!$this->_initProfile('profile_id')) {
                return ;
            }
            $profile = Mage::registry('current_convert_profile');

            // Prepare profile saving data
            if (isset($data)) {
                $profile->addData($data);
            }

            try {
                $profile->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Profile was successfully saved'));
            }
            catch (Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setConvertProfileData($data);
                $this->getResponse()->setRedirect($this->getUrl('*/*/edit', array('id'=>$profile->getId())));
                return;
            }
            if ($this->getRequest()->getParam('continue')) {
                $this->_redirect('*/*/edit', array('id'=>$profile->getId()));
            } else {
                $this->_redirect('*/*');
            }
        }
        else {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Invalid POST data (please check post_max_size and upload_max_filesize settings in you php.ini file)'));
            $this->_redirect('*/*');
        }
    }

    public function runAction()
    {
        $this->_initProfile();
        #$this->loadLayout();

        #$this->_setActiveMenu('system/convert');

        #$this->_addContent(
        #    $this->getLayout()->createBlock('adminhtml/system_convert_profile_run')
        #);
        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/system_convert_profile_run')->toHtml());
        $this->getResponse()->sendResponse();

        #$this->renderLayout();
    }

    public function batchRunAction()
    {
        if ($this->getRequest()->isPost()) {
            $batchId = $this->getRequest()->getPost('batch_id',0);
            $rowIds  = $this->getRequest()->getPost('rows');

            $batchModel = Mage::getModel('dataflow/batch')->load($batchId);
            /* @var $batchModel Mage_Dataflow_Model_Batch */

            if (!$batchModel->getId()) {
                //exit
                return ;
            }
            if (!is_array($rowIds) || count($rowIds) < 1) {
                //exit
                return ;
            }
            if (!$batchModel->getAdapter()) {
                //exit
                return ;
            }

            $batchImportModel = $batchModel->getBatchImportModel();
            $importIds = $batchImportModel->getIdCollection();

            $adapter = Mage::getModel($batchModel->getAdapter());
            $adapter->setBatchParams($batchModel->getParams());

            $errors = array();
            $saved  = 0;

            foreach ($rowIds as $importId) {
                $batchImportModel->load($importId);
                if (!$batchImportModel->getId()) {
                    $errors[] = Mage::helper('dataflow')->__('Skip undefined row');
                    continue;
                }

                $importData = $batchImportModel->getBatchData();
                try {
                    $adapter->saveRow($importData);
                }
                catch (Exception $e) {
                    $errors[] = $e->getMessage();
                    continue;
                }
                $saved ++;
            }

            $result = array(
                'savedRows' => $saved,
                'errors'    => $errors
            );
            $this->getResponse()->setBody(Zend_Json::encode($result));
        }
    }

    public function batchFinishAction()
    {
        if ($batchId = $this->getRequest()->getParam('id')) {
            $batchModel = Mage::getModel('dataflow/batch')->load($batchId);
            /* @var $batchModel Mage_Dataflow_Model_Batch */

            if ($batchModel->getId()) {
                try {
                    $batchModel->beforeFinish();
                }
                catch (Mage_Core_Exception $e) {
                    $result['error'] = $e->getMessage();
                }
                catch (Exception $e) {
                    $result['error'] = Mage::helper('adminhtml')->__('Error while finished process. Please refresh cache');
                }
                $batchModel->delete();
                $this->getResponse()->setBody(Zend_Json::encode($result));
            }
        }
    }

    /**
     * Customer orders grid
     *
     */
    public function historyAction() {
        $this->_initProfile();
        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/system_convert_profile_edit_tab_history')->toHtml());
    }

    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'index':
                $aclResource = 'admin/system/convert/profiles';
                break;
            case 'grid':
                $aclResource = 'admin/system/convert/profiles';
                break;
            case 'run':
                $aclResource = 'admin/system/convert/profiles/run';
                break;
            default:
                $aclResource = 'admin/system/convert/profiles/edit';
                break;
        }

        return Mage::getSingleton('admin/session')->isAllowed($aclResource);
    }
}
