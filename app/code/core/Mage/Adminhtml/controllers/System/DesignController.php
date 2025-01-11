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
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_System_DesignController extends Mage_Adminhtml_Controller_Action
{
    /**
     * ACL resource
     * @see Mage_Adminhtml_Controller_Action::_isAllowed()
     */
    public const ADMIN_RESOURCE = 'system/design';

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

    public function indexAction()
    {
        $this->_title($this->__('System'))->_title($this->__('Design'));

        $this->loadLayout();
        $this->_setActiveMenu('system/design');
        $this->_addContent($this->getLayout()->createBlock('adminhtml/system_design'));
        $this->renderLayout();
    }

    public function gridAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/system_design_grid')->toHtml());
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $this->_title($this->__('System'))->_title($this->__('Design'));

        $this->loadLayout();
        $this->_setActiveMenu('system/design');
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $id  = (int) $this->getRequest()->getParam('id');
        $design    = Mage::getModel('core/design');

        if ($id) {
            $design->load($id);
        }

        $this->_title($design->getId() ? $this->__('Edit Design Change') : $this->__('New Design Change'));

        Mage::register('design', $design);

        $this->_addContent($this->getLayout()->createBlock('adminhtml/system_design_edit'));
        $this->_addLeft($this->getLayout()->createBlock('adminhtml/system_design_edit_tabs', 'design_tabs'));

        $this->renderLayout();
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            if (!empty($data['design'])) {
                $data['design'] = $this->_filterDates($data['design'], ['date_from', 'date_to']);
            }

            $id = (int) $this->getRequest()->getParam('id');

            $design = Mage::getModel('core/design');
            if ($id) {
                $design->load($id);
            }

            $design->setData($data['design']);
            if ($id) {
                $design->setId($id);
            }
            try {
                $design->save();

                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The design change has been saved.'));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')
                    ->addError($e->getMessage())
                    ->setDesignData($data);
                $this->_redirect('*/*/edit', ['id' => $design->getId()]);
                return;
            }
        }

        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            $design = Mage::getModel('core/design')->load($id);

            try {
                $design->delete();

                Mage::getSingleton('adminhtml/session')
                    ->addSuccess($this->__('The design change has been deleted.'));
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')
                    ->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')
                    ->addException($e, $this->__('Cannot delete the design change.'));
            }
        }
        $this->getResponse()->setRedirect($this->getUrl('*/*/'));
    }
}
