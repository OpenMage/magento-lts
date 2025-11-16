<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

use Mage_Cms_Api_Data_BlockInterface as BlockInterface;

/**
 * Cms manage blocks controller
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Cms_BlockController extends Mage_Adminhtml_Controller_Action
{
    /**
     * ACL resource
     * @see Mage_Adminhtml_Controller_Action::_isAllowed()
     */
    public const ADMIN_RESOURCE = 'cms/block';

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
     * Init actions
     *
     * @return $this
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->loadLayout()
            ->_setActiveMenu('cms/block')
            ->_addBreadcrumb(Mage::helper('cms')->__('CMS'), Mage::helper('cms')->__('CMS'))
            ->_addBreadcrumb(Mage::helper('cms')->__('Static Blocks'), Mage::helper('cms')->__('Static Blocks'))
        ;
        return $this;
    }

    /**
     * Index action
     *
     * @throws Mage_Core_Exception
     */
    public function indexAction()
    {
        $this->_title($this->__('CMS'))->_title($this->__('Static Blocks'));

        $this->_initAction();
        $this->renderLayout();
    }

    /**
     * Create new CMS block
     */
    public function newAction()
    {
        // the same form is used to create and edit
        $this->_forward('edit');
    }

    /**
     * Edit CMS block
     *
     * @throws Mage_Core_Exception
     */
    public function editAction()
    {
        $this->_title($this->__('CMS'))->_title($this->__('Static Blocks'));

        // 1. Get ID and create model
        $blockId = $this->getRequest()->getParam(BlockInterface::DATA_ID);
        $model = Mage::getModel('cms/block');

        // 2. Initial checking
        if ($blockId) {
            $model->load($blockId);
            if (!$model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('cms')->__('This block no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $this->_title($model->getId() ? $model->getTitle() : $this->__('New Block'));

        // 3. Set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        // 4. Register model to use later in blocks
        Mage::register('cms_block', $model);

        // 5. Build edit form
        $this->_initAction()
            ->_addBreadcrumb($blockId ? Mage::helper('cms')->__('Edit Block') : Mage::helper('cms')->__('New Block'), $blockId ? Mage::helper('cms')->__('Edit Block') : Mage::helper('cms')->__('New Block'))
            ->renderLayout();
    }

    /**
     * Save action
     *
     * @throws Mage_Core_Exception
     */
    public function saveAction()
    {
        // check if data sent
        if ($data = $this->getRequest()->getPost()) {
            $blockId = $this->getRequest()->getParam(BlockInterface::DATA_ID);
            $model = Mage::getModel('cms/block')->load($blockId);
            if (!$model->getId() && $blockId) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('cms')->__('This block no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }

            // init model and set data

            $model->setData($data);

            // try to save it
            try {
                // save the data
                $model->save();
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('cms')->__('The block has been saved.'));
                // clear previously saved data from session
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', [BlockInterface::DATA_ID => $model->getId()]);
                    return;
                }

                // go to grid
                $this->_redirect('*/*/');
                return;
            } catch (Exception $exception) {
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($exception->getMessage());
                // save data in session
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                // redirect to edit form
                $this->_redirect('*/*/edit', [BlockInterface::DATA_ID => $this->getRequest()->getParam(BlockInterface::DATA_ID)]);
                return;
            }
        }

        $this->_redirect('*/*/');
    }

    /**
     * Delete action
     */
    public function deleteAction()
    {
        // check if we know what should be deleted
        if ($blockId = $this->getRequest()->getParam(BlockInterface::DATA_ID)) {
            try {
                // init model and delete
                $model = Mage::getModel('cms/block');
                $model->load($blockId);
                $model->delete();
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('cms')->__('The block has been deleted.'));
                // go to grid
                $this->_redirect('*/*/');
                return;
            } catch (Exception $exception) {
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($exception->getMessage());
                // go back to edit form
                $this->_redirect('*/*/edit', [BlockInterface::DATA_ID => $blockId]);
                return;
            }
        }

        // display error message
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('cms')->__('Unable to find a block to delete.'));
        // go to grid
        $this->_redirect('*/*/');
    }
}
