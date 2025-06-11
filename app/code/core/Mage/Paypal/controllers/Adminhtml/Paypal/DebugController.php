<?php
class Mage_Paypal_Adminhtml_Paypal_DebugController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('report/paypal/debug')
            ->_addBreadcrumb(
                Mage::helper('paypal')->__('Reports'),
                Mage::helper('paypal')->__('PayPal Debug Log')
            );
        return $this;
    }

    public function indexAction()
    {
        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('paypal/adminhtml_debug'))
            ->renderLayout();
    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('paypal/adminhtml_debug_grid')->toHtml()
        );
    }

    public function massDeleteAction()
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
                    $this->__('Total of %d record(s) have been deleted.', count($debugIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function deleteAllAction()
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

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('report/paypal/debug');
    }
}
