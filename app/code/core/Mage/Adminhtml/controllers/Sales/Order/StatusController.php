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
 * Order status management controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Sales_Order_StatusController extends Mage_Adminhtml_Controller_Action
{
    /**
     * ACL resource
     * @see Mage_Adminhtml_Controller_Action::_isAllowed()
     */
    public const ADMIN_RESOURCE = 'system/order_statuses';

    /**
     * Additional initialization
     */
    protected function _construct()
    {
        $this->setUsedModuleName('Mage_Sales');
    }

    /**
     * Initialize status model based on status code in request
     *
     * @return Mage_Sales_Model_Order_Status | false
     */
    protected function _initStatus()
    {
        $statusCode = $this->getRequest()->getParam('status');
        if ($statusCode) {
            $status = Mage::getModel('sales/order_status')->load($statusCode);
        } else {
            $status = false;
        }
        return $status;
    }

    /**
     * Statuses grid page
     */
    public function indexAction()
    {
        $this->_title($this->__('Sales'))->_title($this->__('Order Statuses'));
        $this->loadLayout()->_setActiveMenu('system/order_statuses')->renderLayout();
    }

    /**
     * New status form
     */
    public function newAction()
    {
        $data = $this->_getSession()->getFormData(true);
        if ($data) {
            $status = Mage::getModel('sales/order_status')
                ->setData($data);
            Mage::register('current_status', $status);
        }
        $this->_title($this->__('Sales'))->_title($this->__('Create New Order Status'));
        $this->loadLayout()
            ->_setActiveMenu('system/order_statuses')
            ->renderLayout();
    }

    /**
     * Editing existing status form
     */
    public function editAction()
    {
        $status = $this->_initStatus();
        if ($status) {
            Mage::register('current_status', $status);
            $this->_title($this->__('Sales'))->_title($this->__('Edit Order Status'));
            $this->loadLayout()
                ->_setActiveMenu('system/order_statuses')
                ->renderLayout();
        } else {
            $this->_getSession()->addError(
                Mage::helper('sales')->__('Order status does not exist.')
            );
            $this->_redirect('*/');
        }
    }

    /**
     * Save status form processing
     */
    public function saveAction()
    {
        $data = $this->getRequest()->getPost();
        $isNew = $this->getRequest()->getParam('is_new');
        if ($data) {
            $statusCode = $this->getRequest()->getParam('status');

            //filter tags in labels/status
            /** @var Mage_Adminhtml_Helper_Data $helper */
            $helper = Mage::helper('adminhtml');
            if ($isNew) {
                $statusCode = $data['status'] = $helper->stripTags($data['status']);
            }
            $data['label'] = $helper->stripTags($data['label']);
            foreach ($data['store_labels'] as &$label) {
                $label = $helper->stripTags($label);
            }

            $status = Mage::getModel('sales/order_status')
                    ->load($statusCode);
            // check if status exist
            if ($isNew && $status->getStatus()) {
                $this->_getSession()->addError(
                    Mage::helper('sales')->__('Order status with the same status code already exist.')
                );
                $this->_getSession()->setFormData($data);
                $this->_redirect('*/*/new');
                return;
            }

            $status->setData($data)
                    ->setStatus($statusCode);
            try {
                $status->save();
                $this->_getSession()->addSuccess(Mage::helper('sales')->__('The order status has been saved.'));
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException(
                    $e,
                    Mage::helper('sales')->__('An error occurred while saving order status. The status has not been added.')
                );
            }
            $this->_getSession()->setFormData($data);
            if ($isNew) {
                $this->_redirect('*/*/new');
            } else {
                $this->_redirect('*/*/edit', ['status' => $this->getRequest()->getParam('status')]);
            }
            return;
        }
        $this->_redirect('*/*/');
    }

    /**
     * Assign status to state form
     */
    public function assignAction()
    {
        $this->_title($this->__('Sales'))->_title($this->__('Assign Order Status to State'));
        $this->loadLayout()
            ->_setActiveMenu('system/order_statuses')
            ->renderLayout();
    }

    /**
     * Save status assignment to state
     */
    public function assignPostAction()
    {
        $data = $this->getRequest()->getPost();
        if ($data) {
            $state  = $this->getRequest()->getParam('state');
            $isDefault = $this->getRequest()->getParam('is_default');
            $status = $this->_initStatus();
            if ($status && $status->getStatus()) {
                try {
                    $status->assignState($state, $isDefault);
                    $this->_getSession()->addSuccess(Mage::helper('sales')->__('The order status has been assigned.'));
                    $this->_redirect('*/*/');
                    return;
                } catch (Mage_Core_Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                } catch (Exception $e) {
                    $this->_getSession()->addException(
                        $e,
                        Mage::helper('sales')->__('An error occurred while assigning order status. Status has not been assigned.')
                    );
                }
            } else {
                $this->_getSession()->addError(Mage::helper('sales')->__('Order status does not exist.'));
            }
            $this->_redirect('*/*/assign');
            return;
        }
        $this->_redirect('*/*/');
    }

    /**
     * Unassign the status from a specific state
     */
    public function unassignAction()
    {
        $state  = $this->getRequest()->getParam('state');
        $status = $this->_initStatus();
        if ($status) {
            try {
                Mage::dispatchEvent('sales_order_status_unassign_before', [
                    'status' => $status, // string {new,     ...}
                    'state'  => $state   // Model  {Pending, ...}
                ]);
                $status->unassignState($state);
                $this->_getSession()->addSuccess(Mage::helper('sales')->__('The order status has been unassigned.'));
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException(
                    $e,
                    Mage::helper('sales')->__('An error occurred while unassigning order status.')
                );
            }
        } else {
            $this->_getSession()->addError(Mage::helper('sales')->__('Order status does not exist.'));
        }
        $this->_redirect('*/*/');
    }
}
