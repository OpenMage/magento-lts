<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml AdminNotification controller
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_NotificationController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_title($this->__('System'))->_title($this->__('Notifications'));

        $this->loadLayout()
            ->_setActiveMenu('system/adminnotification')
            ->_addBreadcrumb(Mage::helper('adminnotification')->__('Messages Inbox'), Mage::helper('adminhtml')->__('Messages Inbox'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/notification_inbox'))
            ->renderLayout();
    }

    public function markAsReadAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            $session = Mage::getSingleton('adminhtml/session');
            $model = Mage::getModel('adminnotification/inbox')
                ->load($id);

            if (!$model->getId()) {
                $session->addError(Mage::helper('adminnotification')->__('Unable to proceed. Please, try again.'));
                $this->_redirect('*/*/');
                return ;
            }

            try {
                $model->setIsRead(1)
                    ->save();
                $session->addSuccess(Mage::helper('adminnotification')->__('The message has been marked as read.'));
            } catch (Mage_Core_Exception $e) {
                $session->addError($e->getMessage());
            } catch (Exception $e) {
                $session->addException($e, Mage::helper('adminnotification')->__('An error occurred while marking notification as read.'));
            }

            $this->_redirectReferer();
            return;
        }
        $this->_redirect('*/*/');
    }

    public function massMarkAsReadAction()
    {
        $session = Mage::getSingleton('adminhtml/session');
        $ids = $this->getRequest()->getParam('notification');
        if (!is_array($ids)) {
            $session->addError(Mage::helper('adminnotification')->__('Please select messages.'));
        } else {
            try {
                foreach ($ids as $id) {
                    $model = Mage::getModel('adminnotification/inbox')
                        ->load($id);
                    if ($model->getId()) {
                        $model->setIsRead(1)
                            ->save();
                    }
                }
                $this->_getSession()->addSuccess(
                    Mage::helper('adminnotification')->__('Total of %d record(s) have been marked as read.', count($ids)),
                );
            } catch (Mage_Core_Exception $e) {
                $session->addError($e->getMessage());
            } catch (Exception $e) {
                $session->addException($e, Mage::helper('adminnotification')->__('An error occurred while marking the messages as read.'));
            }
        }
        $this->_redirect('*/*/');
    }

    public function removeAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            $session = Mage::getSingleton('adminhtml/session');
            $model = Mage::getModel('adminnotification/inbox')
                ->load($id);

            if (!$model->getId()) {
                $this->_redirect('*/*/');
                return ;
            }

            try {
                $model->setIsRemove(1)
                    ->save();
                $session->addSuccess(Mage::helper('adminnotification')->__('The message has been removed.'));
            } catch (Mage_Core_Exception $e) {
                $session->addError($e->getMessage());
            } catch (Exception $e) {
                $session->addException($e, Mage::helper('adminnotification')->__('An error occurred while removing the message.'));
            }

            $this->_redirect('*/*/');
            return;
        }
        $this->_redirect('*/*/');
    }

    public function massRemoveAction()
    {
        $session = Mage::getSingleton('adminhtml/session');
        $ids = $this->getRequest()->getParam('notification');
        if (!is_array($ids)) {
            $session->addError(Mage::helper('adminnotification')->__('Please select messages.'));
        } else {
            try {
                foreach ($ids as $id) {
                    $model = Mage::getModel('adminnotification/inbox')
                        ->load($id);
                    if ($model->getId()) {
                        $model->setIsRemove(1)
                            ->save();
                    }
                }
                $this->_getSession()->addSuccess(
                    Mage::helper('adminnotification')->__('Total of %d record(s) have been removed.', count($ids)),
                );
            } catch (Mage_Core_Exception $e) {
                $session->addError($e->getMessage());
            } catch (Exception $e) {
                $session->addException($e, Mage::helper('adminnotification')->__('An error occurred while removing messages.'));
            }
        }
        $this->_redirectReferer();
    }

    /**
     * @inheritDoc
     */
    protected function _isAllowed()
    {
        $action = strtolower($this->getRequest()->getActionName());
        $acl = match ($action) {
            'massmarkasread', 'markasread' => 'system/adminnotification/mark_as_read',
            'massremove', 'remove' => 'system/adminnotification/remove',
            default => 'system/adminnotification/show_list',
        };
        return Mage::getSingleton('admin/session')->isAllowed($acl);
    }
}
