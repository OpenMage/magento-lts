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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Tag
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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

        $this->getLayout()->getBlock('head')->setTitle(Mage::helper('tag')->__('My Tags'));
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
            $this->getLayout()->getBlock('head')->setTitle(Mage::helper('tag')->__('My Tags'));
            $this->renderLayout();
        }
        else {
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
        if( !Mage::getSingleton('customer/session')->getCustomerId() ) {
            Mage::getSingleton('customer/session')->authenticate($this);
            return;
        }

        if ($tagId = $this->_getTagId()) {
            try {
                $model = Mage::registry('tagModel');
                $model->deactivate();
                $tag = Mage::getModel('tag/tag')->load($tagId)->aggregate();
                Mage::getSingleton('tag/session')->addSuccess(Mage::helper('tag')->__('Your tag was successfully deleted'));
                $this->getResponse()->setRedirect(Mage::getUrl('*/*/', array(
                    self::PARAM_NAME_URL_ENCODED => Mage::helper('core')->urlEncode(Mage::getUrl('customer/account/'))
                )));
                return;
            } catch (Exception $e) {
                Mage::getSingleton('tag/session')->addError(Mage::helper('tag')->__('Unable to remove tag. Please, try again later.'));
            }
        }
        else {
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
