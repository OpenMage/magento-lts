<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

declare(strict_types=1);

class Mage_Paypal_Adminhtml_Paypal_DebugController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Initializes the layout and sets the active menu for the debug log page.
     *
     * @return $this
     */
    protected function _initAction(): self
    {
        $this->loadLayout()
            ->_setActiveMenu('report/paypal/debug')
            ->_addBreadcrumb(
                Mage::helper('paypal')->__('Reports'),
                Mage::helper('paypal')->__('PayPal Debug Log'),
            );
        return $this;
    }

    /**
     * Renders the PayPal debug log grid page.
     *
     * @return void
     */
    public function indexAction(): void
    {
        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('paypal/adminhtml_debug'))
            ->renderLayout();
    }

    /**
     * Renders the debug log grid for AJAX requests.
     *
     * @return void
     */
    public function gridAction(): void
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('paypal/adminhtml_debug_grid')->toHtml(),
        );
    }

    /**
     * Handles the mass deletion of selected debug log entries.
     *
     * @return void
     */
    public function massDeleteAction(): void
    {
        $debugIds = $this->getRequest()->getParam('debug');
        if (!is_array($debugIds)) {
            $this->_getSession()->addError($this->__('Please select log(s) to delete.'));
        } else {
            try {
                foreach ($debugIds as $debugId) {
                    Mage::getModel('paypal/debug')->load($debugId)->delete();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) have been deleted.', count($debugIds)),
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Handles the deletion of all debug log entries.
     *
     * @return void
     */
    public function deleteAllAction(): void
    {
        try {
            $collection = Mage::getModel('paypal/debug')->getCollection();
            foreach ($collection as $item) {
                $item->delete();
            }
            $this->_getSession()->addSuccess($this->__('All debug logs have been deleted.'));
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Checks if the current user has permission to access this controller.
     *
     * @return bool
     */
    protected function _isAllowed(): bool
    {
        return Mage::getSingleton('admin/session')->isAllowed('report/paypal/debug');
    }
}
