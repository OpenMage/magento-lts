<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Tag
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer tags controller
 *
 * @category   Mage
 * @package    Mage_Tag
 */
class Mage_Tag_CustomerController extends Mage_Core_Controller_Front_Action
{
    /**
     * @return int|false
     * @throws Mage_Core_Exception
     */
    protected function _getTagId()
    {
        $tagId = (int) $this->getRequest()->getParam('tagId');
        if ($tagId) {
            $customerId = $this->getCustomerSession()->getCustomerId();
            $model = Mage::getModel('tag/tag_relation');
            $model->loadByTagCustomer(null, $tagId, $customerId);
            Mage::register('tagModel', $model);
            return $model->getTagId();
        }
        return false;
    }

    public function indexAction()
    {
        if (!$this->getCustomerSession()->isLoggedIn()) {
            $this->getCustomerSession()->authenticate($this);
            return;
        }

        $this->loadLayout();
        $this->_initLayoutMessages($this->getTagSessionStorage());
        $this->_initLayoutMessages($this->getCatalogSessionStorage());

        $navigationBlock = $this->getLayout()->getBlock('customer_account_navigation');
        if ($navigationBlock) {
            $navigationBlock->setActive('tag/customer');
        }

        $block = $this->getLayout()->getBlock('customer_tags');
        if ($block) {
            $block->setRefererUrl($this->_getRefererUrl());
        }

        $this->getLayout()->getBlock('head')->setTitle(Mage::helper('tag')->__('My Tags'));
        $this->renderLayout();
    }

    public function viewAction()
    {
        if (!$this->getCustomerSession()->isLoggedIn()) {
            $this->getCustomerSession()->authenticate($this);
            return;
        }

        $tagId = $this->_getTagId();
        if ($tagId) {
            Mage::register('tagId', $tagId);
            $this->loadLayout();
            $this->_initLayoutMessages($this->getTagSessionStorage());

            $navigationBlock = $this->getLayout()->getBlock('customer_account_navigation');
            if ($navigationBlock) {
                $navigationBlock->setActive('tag/customer');
            }

            $this->_initLayoutMessages($this->getCheckoutSessionStorage());
            $this->getLayout()->getBlock('head')->setTitle(Mage::helper('tag')->__('My Tags'));
            $this->renderLayout();
        } else {
            $this->_forward('noRoute');
        }
    }

    /**
     * @deprecated after 1.3.2.3
     * This functionality was removed
     *
     */
    public function editAction()
    {
        $this->_forward('noRoute');
    }

    public function removeAction()
    {
        if (!$this->getCustomerSession()->isLoggedIn()) {
            $this->getCustomerSession()->authenticate($this);
            return;
        }

        $tagId = $this->_getTagId();
        if ($tagId) {
            try {
                $model = Mage::registry('tagModel');
                $model->deactivate();
                $tag = Mage::getModel('tag/tag')->load($tagId)->aggregate();
                $this->getTagSession()->addSuccess(Mage::helper('tag')->__('The tag has been deleted.'));
                $this->getResponse()->setRedirect(Mage::getUrl('*/*/', [
                    self::PARAM_NAME_URL_ENCODED => Mage::helper('core')->urlEncode(Mage::getUrl('customer/account/'))
                ]));
                return;
            } catch (Exception $e) {
                $this->getTagSession()->addError(Mage::helper('tag')->__('Unable to remove tag. Please, try again later.'));
            }
        } else {
            $this->_forward('noRoute');
        }
    }

    /**
     * @deprecated after 1.3.2.3
     * This functionality was removed
     *
     */
    public function saveAction()
    {
        $this->_forward('noRoute');
    }
}
