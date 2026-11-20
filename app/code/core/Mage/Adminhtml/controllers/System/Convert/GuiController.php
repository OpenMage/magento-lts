<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

include_once 'ProfileController.php';

/**
 * Convert GUI admin controller
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_System_Convert_GuiController extends Mage_Adminhtml_System_Convert_ProfileController
{
    /**
     * ACL resource
     * @see Mage_Adminhtml_Controller_Action::_isAllowed()
     */
    public const ADMIN_RESOURCE = 'admin/system/convert/gui';

    /**
     * Profiles list action
     * @return void
     */
    #[Override]
    public function indexAction()
    {
        $this->_title($this->__('System'))
             ->_title($this->__('Import and Export'))
             ->_title($this->__('Profiles'));

        if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }

        $this->loadLayout();

        /**
         * Set active menu item
         */
        $this->_setActiveMenu('system/convert/gui');

        /**
         * Append profiles block to content
         */
        $this->_addContent(
            $this->getLayout()->createBlock('adminhtml/system_convert_gui', 'convert_profile'),
        );

        /**
         * Add breadcrumb item
         */
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Import/Export'), Mage::helper('adminhtml')->__('Import/Export'));
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Profiles'), Mage::helper('adminhtml')->__('Profiles'));

        $this->renderLayout();
    }

    #[Override]
    public function gridAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/system_convert_gui_grid')->toHtml());
    }

    /**
     * Profile edit action
     * @return void
     */
    #[Override]
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

        $this->_setActiveMenu('system/convert/gui');

        $this->_addContent(
            $this->getLayout()->createBlock('adminhtml/system_convert_gui_edit'),
        );

        /**
         * Append edit tabs to left block
         */
        $this->_addLeft($this->getLayout()->createBlock('adminhtml/system_convert_gui_edit_tabs'));

        $this->renderLayout();
    }

    /**
     * Run a profile built with the profile wizard
     *
     * Without the Advanced Profiles permission the profile runs the actions XML the wizard
     * generates from its fields, not the stored XML. A profile saved before the wizard escaped
     * its fields, or saved without them, can store XML the wizard would not generate.
     *
     * @return void
     * @throws Mage_Core_Exception
     */
    #[Override]
    public function runAction()
    {
        if (!$this->_initProfile()) {
            return;
        }

        /** @var Mage_Dataflow_Model_Profile $profile */
        $profile = Mage::registry('current_convert_profile');
        if ((int) $profile->getId() !== 0
            && !Mage::getSingleton('admin/session')->isAllowed(Mage_Adminhtml_System_Convert_ProfileController::ADMIN_RESOURCE)
        ) {
            try {
                if (!is_array($profile->getGuiData())) {
                    Mage::throwException($this->__('Invalid profile data.'));
                }

                $profile->_parseGuiData();
            } catch (Exception $exception) {
                Mage::getSingleton('adminhtml/session')->addError($exception->getMessage());
                $this->_redirect('*/*/edit', ['id' => $profile->getId()]);
                return;
            }
        }

        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Save a profile built with the profile wizard
     *
     * The actions XML of a GUI profile is generated from the wizard fields, so a posted
     * actions_xml is discarded and the wizard fields are required. The profile saved is the
     * one _isAllowed() checked, whatever profile_id the post carries.
     *
     * @return void
     * @throws Mage_Core_Exception
     */
    #[Override]
    public function saveAction()
    {
        $request = $this->getRequest();
        $post = $request->getPost();
        if (is_array($post) && $post !== []) {
            if (!is_array($post['gui_data'] ?? null)
                || !is_string($post['entity_type'] ?? null)
                || !isset(Mage_Dataflow_Model_Profile::GUI_ADAPTERS[$post['entity_type']])
            ) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('Invalid profile data.'));
                $this->_redirect('*/*');
                return;
            }

            // _initProfile() loads this id, and addData() would otherwise copy the posted one onto it
            $request->setParam('profile_id', (int) $request->getParam('profile_id'));
            unset($post['actions_xml'], $post['profile_id']);
            $request->setPost($post);
        }

        parent::saveAction();
    }

    /**
     * The Profiles permission covers only profiles built with the profile wizard
     *
     * Advanced Profiles (no entity type) and their batches need the Advanced Profiles
     * permission, which their own controller checks.
     */
    #[Override]
    protected function _isAllowed(): bool
    {
        if (!parent::_isAllowed()) {
            return false;
        }

        $request = $this->getRequest();
        $action = strtolower($request->getActionName());
        if ($action === 'batchrun' || $action === 'batchfinish') {
            $batchId = (int) ($action === 'batchrun' ? $request->getPost('batch_id') : $request->getParam('id'));
            return $batchId === 0 || $this->_isGuiBatch($this->_loadBatch($batchId));
        }

        foreach (['id', 'profile_id'] as $param) {
            $profileId = (int) $request->getParam($param);
            if ($profileId !== 0 && $this->_getGuiEntityType($profileId) === null) {
                return false;
            }
        }

        return true;
    }

    /**
     * Entity type of a profile built with the profile wizard, null for an Advanced Profile or a missing one
     */
    protected function _getGuiEntityType(int $profileId): ?string
    {
        $entityType = Mage::getModel('dataflow/profile')->load($profileId)->getEntityType();
        return is_string($entityType) && isset(Mage_Dataflow_Model_Profile::GUI_ADAPTERS[$entityType]) ? $entityType : null;
    }

    /**
     * Whether a wizard profile that still exists started the batch, with the adapter the wizard declares
     */
    protected function _isGuiBatch(Mage_Dataflow_Model_Batch $batch): bool
    {
        $entityType = $this->_getGuiEntityType((int) $batch->getProfileId());
        $adapter = $batch->getAdapter();
        return $entityType !== null
            && ($adapter === null || $adapter === Mage_Dataflow_Model_Profile::GUI_ADAPTERS[$entityType]);
    }

    protected function _loadBatch(int $batchId): Mage_Dataflow_Model_Batch
    {
        return Mage::getModel('dataflow/batch')->load($batchId);
    }

    /**
     * @return void
     */
    public function uploadAction()
    {
        $this->_initProfile();
        Mage::registry('current_convert_profile');
    }

    /**
     * @return void
     */
    public function uploadPostAction()
    {
        $this->_initProfile();
        Mage::registry('current_convert_profile');
    }

    /**
     * @return void
     */
    public function downloadAction()
    {
        $filename = $this->getRequest()->getParam('filename');
        if (!$filename || str_contains($filename, '..') || $filename[0] === '.') {
            return;
        }

        $this->_initProfile();
        Mage::registry('current_convert_profile');
    }
}
