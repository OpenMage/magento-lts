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
 * @category   Mage
 * @package    Mage_Tag
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer tags controller
 *
 * @category   Mage
 * @package    Mage_Tag
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Tag_CustomerController extends Mage_Core_Controller_Front_Action
{
    protected function _getTagId()
    {
        $tagId = (int) $this->getRequest()->getParam('tagId');
        if ($tagId) {
            $customerId = Mage::getSingleton('customer/session')->getCustomerId();
            $model = Mage::getModel('tag/tag_relation');
            $model->loadByTagCustomer(null, $tagId, $customerId);
            Mage::register('tagModel', $model);
            return $model->getTagId();
        }
        return false;
    }

    public function indexAction()
    {
        if( !Mage::getSingleton('customer/session')->getCustomerId() ) {
            Mage::getSingleton('customer/session')->authenticate($this);
            return;
        }

        $this->loadLayout();
        $this->_initLayoutMessages('tag/session');
         $this->_initLayoutMessages('catalog/session');

        if ($navigationBlock = $this->getLayout()->getBlock('customer_account_navigation')) {
            $navigationBlock->setActive('tag/customer');
        }

        if ($block = $this->getLayout()->getBlock('customer_tags')) {
            $block->setRefererUrl($this->_getRefererUrl());
        }

        $this->renderLayout();
    }

    public function viewAction()
    {
        if( !Mage::getSingleton('customer/session')->getCustomerId() ) {
            Mage::getSingleton('customer/session')->authenticate($this);
            return;
        }
        if ($tagId = $this->_getTagId()) {
            Mage::register('tagId', $tagId);
            $this->loadLayout();
            $this->_initLayoutMessages('tag/session');

            if ($navigationBlock = $this->getLayout()->getBlock('customer_account_navigation')) {
                $navigationBlock->setActive('tag/customer');
            }

            $this->_initLayoutMessages('checkout/session');
            $this->renderLayout();
        }
        else {
            $this->_forward('noRoute');
        }
    }

    public function editAction()
    {
        if( !Mage::getSingleton('customer/session')->getCustomerId() ) {
            Mage::getSingleton('customer/session')->authenticate($this);
            return;
        }

        if ($tagId = $this->_getTagId()) {
            $this->loadLayout();
            $this->_initLayoutMessages('tag/session');
            $this->_initLayoutMessages('customer/session');
            if ($navigationBlock = $this->getLayout()->getBlock('customer_account_navigation')) {
                $navigationBlock->setActive('tag/customer');
            }
            $this->renderLayout();
        }
        else {
            $this->_forward('noRoute');
        }
    }

    public function removeAction()
    {
        if( !Mage::getSingleton('customer/session')->getCustomerId() ) {
            Mage::getSingleton('customer/session')->authenticate($this);
            return;
        }

        if ($tagId = $this->_getTagId()) {
            try {
                $model = Mage::registry('tagModel');
                $model->deactivate();
                $tag = Mage::getModel('tag/tag')->load($tagId)->aggregate();
                Mage::getSingleton('tag/session')->addSuccess($this->__('You tag was successfully deleted'));
                $this->getResponse()->setRedirect(Mage::getUrl('*/*/'));
                return;
            } catch (Exception $e) {
                Mage::getSingleton('tag/session')->addError($this->__('Unable to remove tag. Please, try again later.'));
            }
        }
        else {
            $this->_forward('noRoute');
        }
    }

    public function saveAction()
    {
        if( !Mage::getSingleton('customer/session')->getCustomerId() ) {
            Mage::getSingleton('customer/session')->authenticate($this);
            return;
        }

        $tagId      = (int) $this->getRequest()->getParam('tagId');
        $customerId = Mage::getSingleton('customer/session')->getCustomerId();
        $tagName    = (string) $this->getRequest()->getPost('tagName');

        if (strlen($tagName) === 0) {
            Mage::getSingleton('tag/session')->addError($this->__('Tag can\'t be empty.'));
            $this->_redirect('*/*/edit', array('tagId'=>$tagId));
            return;
        }

        if($tagId) {
            try {
                $productId  = 0;
                $isNew      = false;
                $message    = false;
                $storeId    = Mage::app()->getStore()->getId();

                $tagModel = Mage::getModel('tag/tag');
                $tagModel->load($tagId);

                if( $tagModel->getName() != $tagName ) {
                    $tagModel->loadByName($tagName);

                    if($tagModel->getId()) {
                        $status = $tagModel->getStatus();
                    }
                    else {
                        $isNew  = true;
                        $message= $this->__('Thank you. Your tag has been accepted for moderation.');
                        $status = $tagModel->getPendingStatus();
                    }

                    $tagModel->setName($tagName)
                        ->setStatus($status)
                        ->setStoreId($storeId)
                        ->save();
                }

                $tagRalationModel = Mage::getModel('tag/tag_relation');
                $tagRalationModel->loadByTagCustomer(null, $tagId, $customerId, $storeId);

                if ($tagRalationModel->getCustomerId() == $customerId ) {
                    $productIds = $tagRalationModel->getProductIds();
                    if ($tagRalationModel->getTagId()!=$tagModel->getId()) {
                        $tagRalationModel->deactivate();
                    } else {
                        $tagRalationModel->delete();
                    }

                    foreach ($productIds as $productId) {
                        $newTagRalationModel = Mage::getModel('tag/tag_relation')
                            ->setTagId($tagModel->getId())
                            ->setCustomerId($customerId)
                            ->setStoreId($storeId)
                            ->setActive(true)
                            ->setProductId($productId)
                            ->save();
                    }
                }

                if( $tagModel->getId() ) {
                    $tagModel->aggregate();
                    $this->getResponse()->setRedirect(Mage::getUrl('*/*/'));
                }
                $message = ($message) ? $message : $this->__('You tag was successfully saved');
                Mage::getSingleton('tag/session')->addSuccess($message);
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('tag/session')->addError(
                    $this->__('Unable to save your tag. Please, try again later.')
                );
            }
        }
        $this->_redirectReferer();
    }
}
