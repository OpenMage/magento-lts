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
 * @category   Mage
 * @package    Mage_Customer
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Customer address controller
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_AddressController extends Mage_Core_Controller_Front_Action
{
    /**
     * Retrieve customer session object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    public function preDispatch()
    {
        parent::preDispatch();

        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }
    }

    /**
     * Customer addresses list
     */
    public function indexAction()
    {
        if (count($this->_getSession()->getCustomer()->getAddresses())) {
            $this->loadLayout();
            $this->_initLayoutMessages('customer/session');
            $this->_initLayoutMessages('catalog/session');
            
            if ($block = $this->getLayout()->getBlock('address_book')) {
                $block->setRefererUrl($this->_getRefererUrl());
            }
            $this->renderLayout();
        }
        else {
            $this->getResponse()->setRedirect(Mage::getUrl('*/*/new'));
        }
    }

    public function editAction()
    {
        $this->_forward('form');
    }

    public function newAction()
    {
        $this->_forward('form');
    }

    /**
     * Address book form
     */
    public function formAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        if ($navigationBlock = $this->getLayout()->getBlock('customer_account_navigation')) {
            $navigationBlock->setActive('customer/address');
        }
        $this->renderLayout();
    }

    public function formPostAction()
    {
        // Save data
        if ($this->getRequest()->isPost()) {
            $address = Mage::getModel('customer/address')
                ->setData($this->getRequest()->getPost())
                ->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId())
                ->setIsDefaultBilling($this->getRequest()->getParam('default_billing', false))
                ->setIsDefaultShipping($this->getRequest()->getParam('default_shipping', false));
            $addressId = $this->getRequest()->getParam('id');
            if ($addressId) {
                $customerAddress = $this->_getSession()->getCustomer()->getAddressById($addressId);
                if ($customerAddress->getId() && $customerAddress->getCustomerId() == $this->_getSession()->getCustomerId()) {
                    $address->setId($addressId);
                }
                else {
                    $address->setId(null);
                }
            }
            else {
                $address->setId(null);
            }
            try {
                $accressValidation = $address->validate();
                if (true === $accressValidation) {
                    $address->save();
                    $this->_getSession()->addSuccess($this->__('The address was successfully saved'));
                    $this->_redirectSuccess(Mage::getUrl('*/*/index', array('_secure'=>true)));
                    return;
                } else {
                    $this->_getSession()->setAddressFormData($this->getRequest()->getPost());
                    if (is_array($accressValidation)) {
                        foreach ($accressValidation as $errorMessage) {
                        	$this->_getSession()->addError($errorMessage);
                        }
                    } else {
                        $this->_getSession()->addError($this->__('Can\'t save address'));
                    }
                }
            }
            catch (Mage_Core_Exception $e) {
                $this->_getSession()->setAddressFormData($this->getRequest()->getPost())
                    ->addException($e, $e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->setAddressFormData($this->getRequest()->getPost())
                    ->addException($e, $this->__('Can\'t save address'));
            }
        }
        $this->_redirectError(Mage::getUrl('*/*/edit', array('id'=>$address->getId())));
    }

    public function deleteAction()
    {
        $addressId = $this->getRequest()->getParam('id', false);

        if ($addressId) {
            $address = Mage::getModel('customer/address')->load($addressId);

            // Validate address_id <=> customer_id
            if ($address->getCustomerId() != $this->_getSession()->getCustomerId()) {
                $this->_getSession()->addError($this->__('The address does not belong to this customer'));
                $this->getResponse()->setRedirect(Mage::getUrl('*/*/index'));
                return;
            }

            try {
                $address->delete();
                $this->_getSession()->addSuccess($this->__('The address was successfully deleted'));
            }
            catch (Exception $e){
                $this->_getSession()->addError($this->__('There was an error while deleting the address'));
            }
        }
        $this->getResponse()->setRedirect(Mage::getUrl('*/*/index'));
    }
}