<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Convert Advanced admin controller
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_System_Convert_ProfileController extends Mage_Adminhtml_Controller_Action
{
    /**
     * ACL resource
     * @see Mage_Adminhtml_Controller_Action::_isAllowed()
     */
    public const ADMIN_RESOURCE = 'admin/system/convert/profiles';

    protected function _initProfile($idFieldName = 'id')
    {
        $this->_title($this->__('System'))
             ->_title($this->__('Import and Export'))
             ->_title($this->__('Profiles'));

        $profileId = (int) $this->getRequest()->getParam($idFieldName);
        $profile = Mage::getModel('dataflow/profile');

        if ($profileId) {
            $profile->load($profileId);
            if (!$profile->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    $this->__('The profile you are trying to save no longer exists'),
                );
                $this->_redirect('*/*');
                return false;
            }
        }

        $profile->setAdminUserId(Mage::getSingleton('admin/session')->getUser()->getId());
        Mage::register('current_convert_profile', $profile);

        return $this;
    }

    /**
     * Profiles list action
     */
    public function indexAction()
    {
        $this->_title($this->__('System'))
             ->_title($this->__('Import and Export'))
             ->_title($this->__('Advanced Profiles'));

        if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }

        $this->loadLayout();

        /**
         * Set active menu item
         */
        $this->_setActiveMenu('system/convert/profiles');

        /**
         * Append profiles block to content
         */
        $this->_addContent(
            $this->getLayout()->createBlock('adminhtml/system_convert_profile', 'convert_profile'),
        );

        /**
         * Add breadcrumb item
         */
        $this->_addBreadcrumb(
            Mage::helper('adminhtml')->__('Import/Export'),
            Mage::helper('adminhtml')->__('Import/Export Advanced'),
        );
        $this->_addBreadcrumb(
            Mage::helper('adminhtml')->__('Advanced Profiles'),
            Mage::helper('adminhtml')->__('Advanced Profiles'),
        );

        $this->renderLayout();
    }

    public function gridAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('adminhtml/system_convert_profile_grid')->toHtml(),
        );
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

        $this->_title($profile->getId() ? $profile->getName() : $this->__('New Profile'));

        $this->_setActiveMenu('system/convert/profiles');

        $this->_addContent(
            $this->getLayout()->createBlock('adminhtml/system_convert_profile_edit'),
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
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('The profile has been deleted.'),
                );
            } catch (Exception $e) {
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
                return;
            }

            $profile = Mage::registry('current_convert_profile');

            // Prepare profile saving data
            if (isset($data)) {
                $profile->addData($data);
            }

            try {
                $profile->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('The profile has been saved.'),
                );
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setConvertProfileData($data);
                $this->getResponse()->setRedirect($this->getUrl('*/*/edit', ['id' => $profile->getId()]));
                return;
            }

            if ($this->getRequest()->getParam('continue')) {
                $this->_redirect('*/*/edit', ['id' => $profile->getId()]);
            } else {
                $this->_redirect('*/*');
            }
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                $this->__('Invalid POST data (please check post_max_size and upload_max_filesize settings in your php.ini file).'),
            );
            $this->_redirect('*/*');
        }
    }

    public function runAction()
    {
        $this->_initProfile();
        $this->loadLayout();
        $this->renderLayout();
    }

    public function batchRunAction()
    {
        if ($this->getRequest()->isPost()) {
            $batchId = $this->getRequest()->getPost('batch_id', 0);
            $rowIds  = $this->getRequest()->getPost('rows');

            /** @var Mage_Dataflow_Model_Batch $batchModel */
            $batchModel = Mage::getModel('dataflow/batch')->load($batchId);

            if (!$batchModel->getId()) {
                return;
            }

            if (!is_array($rowIds) || count($rowIds) < 1) {
                return;
            }

            if (!$batchModel->getAdapter()) {
                return;
            }

            $batchImportModel = $batchModel->getBatchImportModel();
            $importIds = $batchImportModel->getIdCollection();

            /** @var Mage_Catalog_Model_Convert_Adapter_Product $adapter */
            $adapter = Mage::getModel($batchModel->getAdapter());
            $adapter->setBatchParams($batchModel->getParams());

            $errors = [];
            $saved  = 0;
            foreach ($rowIds as $importId) {
                $batchImportModel->load($importId);
                if (!$batchImportModel->getId()) {
                    $errors[] = Mage::helper('dataflow')->__('Skip undefined row.');
                    continue;
                }

                try {
                    $importData = $batchImportModel->getBatchData();
                    $adapter->saveRow($importData);
                } catch (Exception $e) {
                    $errors[] = $e->getMessage();
                    continue;
                }

                $saved++;
            }

            if (method_exists($adapter, 'getEventPrefix')) {
                /**
                 * Event for process rules relations after products import
                 */
                Mage::dispatchEvent($adapter->getEventPrefix() . '_finish_before', [
                    'adapter' => $adapter,
                ]);

                /**
                 * Clear affected ids for adapter possible reuse
                 */
                $adapter->clearAffectedEntityIds();
            }

            $result = [
                'savedRows' => $saved,
                'errors'    => $errors,
            ];
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }

    public function batchFinishAction()
    {
        $batchId = $this->getRequest()->getParam('id');
        if ($batchId) {
            $batchModel = Mage::getModel('dataflow/batch')->load($batchId);
            /** @var Mage_Dataflow_Model_Batch $batchModel */

            if ($batchModel->getId()) {
                $result = [];
                try {
                    $batchModel->beforeFinish();
                } catch (Mage_Core_Exception $e) {
                    $result['error'] = $e->getMessage();
                } catch (Exception) {
                    $result['error'] = Mage::helper('adminhtml')->__('An error occurred while finishing process. Please refresh the cache');
                }

                $batchModel->delete();
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            }
        }
    }

    /**
     * Customer orders grid
     */
    public function historyAction()
    {
        $this->_initProfile();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('adminhtml/system_convert_profile_edit_tab_history')->toHtml(),
        );
    }
}
