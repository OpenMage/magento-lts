<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

declare(strict_types=1);

class Mage_Paypal_Adminhtml_Paypal_WebhookController extends Mage_Adminhtml_Controller_Action
{
    /**
     * @return $this
     */
    protected function _initAction(): self
    {
        $this->loadLayout()
            ->_setActiveMenu('report/paypal/webhook')
            ->_addBreadcrumb(
                Mage::helper('paypal')->__('Reports'),
                Mage::helper('paypal')->__('PayPal Webhook Events'),
            );
        return $this;
    }

    public function indexAction(): void
    {
        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('paypal/adminhtml_webhook'))
            ->renderLayout();
    }

    public function gridAction(): void
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('paypal/adminhtml_webhook_grid')->toHtml(),
        );
    }

    public function viewAction(): void
    {
        $event = Mage::getModel('paypal/webhook_event')->load((int) $this->getRequest()->getParam('id'));
        if (!$event->getId()) {
            $this->_getSession()->addError($this->__('Webhook event was not found.'));
            $this->_redirect('*/*/index');
            return;
        }

        $body = [
            'event'   => $event->getData(),
            'payload' => $event->getPayload(),
        ];

        $this->getResponse()
            ->setHeader('Content-Type', 'application/json', true)
            ->setBody((string) json_encode($body, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }

    public function massReprocessAction(): void
    {
        $eventIds = $this->getRequest()->getParam('webhook');
        if (!is_array($eventIds)) {
            $this->_getSession()->addError($this->__('Please select webhook event(s) to reprocess.'));
            $this->_redirect('*/*/index');
            return;
        }

        try {
            foreach ($eventIds as $eventId) {
                Mage::getModel('paypal/webhook_event')->load($eventId)
                    ->setStatus(Mage_Paypal_Model_Webhook_Event::STATUS_VERIFIED)
                    ->setProcessingAttempts(0)
                    ->setLastError(null)
                    ->setProcessedAt(null)
                    ->setUpdatedAt(Varien_Date::now())
                    ->save();
            }

            $this->_getSession()->addSuccess(
                $this->__('Total of %d webhook event(s) have been queued for reprocessing.', count($eventIds)),
            );
        } catch (Exception $exception) {
            $this->_getSession()->addError($exception->getMessage());
        }

        $this->_redirect('*/*/index');
    }

    public function massDeleteAction(): void
    {
        $eventIds = $this->getRequest()->getParam('webhook');
        if (!is_array($eventIds)) {
            $this->_getSession()->addError($this->__('Please select webhook event(s) to delete.'));
            $this->_redirect('*/*/index');
            return;
        }

        try {
            foreach ($eventIds as $eventId) {
                Mage::getModel('paypal/webhook_event')->load($eventId)->delete();
            }

            $this->_getSession()->addSuccess(
                $this->__('Total of %d webhook event(s) have been deleted.', count($eventIds)),
            );
        } catch (Exception $exception) {
            $this->_getSession()->addError($exception->getMessage());
        }

        $this->_redirect('*/*/index');
    }

    /**
     * Checks if the current user has permission to access this controller.
     */
    #[Override]
    protected function _isAllowed(): bool
    {
        return Mage::getSingleton('admin/session')->isAllowed('report/paypal/webhook');
    }
}
