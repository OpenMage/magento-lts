<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Customer
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2017-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer module observer
 *
 * @category   Mage
 * @package    Mage_Customer
 */
class Mage_Customer_Model_Observer
{
    /**
     * VAT ID validation processed flag code
     */
    public const VIV_PROCESSED_FLAG = 'viv_after_address_save_processed';

    /**
     * VAT ID validation currently saved address flag
     */
    public const VIV_CURRENTLY_SAVED_ADDRESS = 'currently_saved_address';

    /**
     * Check whether specified billing address is default for its customer
     *
     * @param Mage_Customer_Model_Address $address
     * @return bool
     */
    protected function _isDefaultBilling($address)
    {
        return ($address->getId() && $address->getId() == $address->getCustomer()->getDefaultBilling())
            || $address->getIsPrimaryBilling() || $address->getIsDefaultBilling();
    }

    /**
     * Check whether specified shipping address is default for its customer
     *
     * @param Mage_Customer_Model_Address $address
     * @return bool
     */
    protected function _isDefaultShipping($address)
    {
        return ($address->getId() && $address->getId() == $address->getCustomer()->getDefaultShipping())
            || $address->getIsPrimaryShipping() || $address->getIsDefaultShipping();
    }

    /**
     * Check whether specified address should be processed in after_save event handler
     *
     * @param Mage_Customer_Model_Address $address
     * @return bool
     */
    protected function _canProcessAddress($address)
    {
        if ($address->getForceProcess()) {
            return true;
        }

        if (Mage::registry(self::VIV_CURRENTLY_SAVED_ADDRESS) != $address->getId()) {
            return false;
        }

        $configAddressType = Mage::helper('customer/address')->getTaxCalculationAddressType();
        if ($configAddressType == Mage_Customer_Model_Address_Abstract::TYPE_SHIPPING) {
            return $this->_isDefaultShipping($address);
        }
        return $this->_isDefaultBilling($address);
    }

    /**
     * Before load layout event handler
     *
     * @param Varien_Event_Observer $observer
     */
    public function beforeLoadLayout($observer)
    {
        $loggedIn = Mage::getSingleton('customer/session')->isLoggedIn();

        $observer->getEvent()->getLayout()->getUpdate()
           ->addHandle('customer_logged_' . ($loggedIn ? 'in' : 'out'));
    }

    /**
     * Address before save event handler
     *
     * @param Varien_Event_Observer $observer
     */
    public function beforeAddressSave($observer)
    {
        if (Mage::registry(self::VIV_CURRENTLY_SAVED_ADDRESS)) {
            Mage::unregister(self::VIV_CURRENTLY_SAVED_ADDRESS);
        }

        /** @var Mage_Customer_Model_Address $customerAddress */
        $customerAddress = $observer->getCustomerAddress();
        if ($customerAddress->getId()) {
            Mage::register(self::VIV_CURRENTLY_SAVED_ADDRESS, $customerAddress->getId());
        } else {
            $configAddressType = Mage::helper('customer/address')->getTaxCalculationAddressType();

            $forceProcess = ($configAddressType == Mage_Customer_Model_Address_Abstract::TYPE_SHIPPING)
                ? $customerAddress->getIsDefaultShipping() : $customerAddress->getIsDefaultBilling();

            if ($forceProcess) {
                $customerAddress->setForceProcess(true);
            } else {
                Mage::register(self::VIV_CURRENTLY_SAVED_ADDRESS, 'new_address');
            }
        }
    }

    /**
     * Address after save event handler
     *
     * @param Varien_Event_Observer $observer
     */
    public function afterAddressSave($observer)
    {
        /** @var Mage_Customer_Model_Address $customerAddress */
        $customerAddress = $observer->getCustomerAddress();
        $customer = $customerAddress->getCustomer();

        $store = Mage::app()->getStore()->isAdmin() ? $customer->getStore() : null;
        if (!Mage::helper('customer/address')->isVatValidationEnabled($store)
            || Mage::registry(self::VIV_PROCESSED_FLAG)
            || !$this->_canProcessAddress($customerAddress)
        ) {
            return;
        }

        try {
            Mage::register(self::VIV_PROCESSED_FLAG, true);

            /** @var Mage_Customer_Helper_Data $customerHelper */
            $customerHelper = Mage::helper('customer');

            if ($customerAddress->getVatId() == ''
                || !Mage::helper('core')->isCountryInEU($customerAddress->getCountry())
            ) {
                $defaultGroupId = $customerHelper->getDefaultCustomerGroupId($customer->getStore());

                if (!$customer->getDisableAutoGroupChange() && $customer->getGroupId() != $defaultGroupId) {
                    $customer->setGroupId($defaultGroupId);
                    $customer->save();
                }
            } else {
                $result = $customerHelper->checkVatNumber(
                    $customerAddress->getCountryId(),
                    $customerAddress->getVatId(),
                );

                $newGroupId = $customerHelper->getCustomerGroupIdBasedOnVatNumber(
                    $customerAddress->getCountryId(),
                    $result,
                    $customer->getStore(),
                );

                if (!$customer->getDisableAutoGroupChange() && $customer->getGroupId() != $newGroupId) {
                    $customer->setGroupId($newGroupId);
                    $customer->save();
                }

                if (!Mage::app()->getStore()->isAdmin()) {
                    $validationMessage = Mage::helper('customer')->getVatValidationUserMessage(
                        $customerAddress,
                        $customer->getDisableAutoGroupChange(),
                        $result,
                    );

                    if (!$validationMessage->getIsError()) {
                        Mage::getSingleton('customer/session')->addSuccess($validationMessage->getMessage());
                    } else {
                        Mage::getSingleton('customer/session')->addError($validationMessage->getMessage());
                    }
                }
            }
        } catch (Exception $e) {
            Mage::register(self::VIV_PROCESSED_FLAG, false, true);
        }
    }

    /**
     * Revert emulated customer group_id
     *
     * @param Varien_Event_Observer $observer
     */
    public function quoteSubmitAfter($observer)
    {
        /** @var Mage_Customer_Model_Customer $customer */
        $customer = $observer->getQuote()->getCustomer();

        if (!Mage::helper('customer/address')->isVatValidationEnabled($customer->getStore())) {
            return;
        }

        if (!$customer->getId()) {
            return;
        }

        $customer->setGroupId(
            $customer->getOrigData('group_id'),
        );
        $customer->save();
    }

    /**
     * Clear customer flow password table
     *
     */
    public function deleteCustomerFlowPassword()
    {
        $resource   = Mage::getSingleton('core/resource');
        $connection = $resource->getConnection('write');
        $condition  = ['requested_date < ?' => Mage::getModel('core/date')->date(null, '-1 day')];
        $connection->delete($resource->getTableName('customer_flowpassword'), $condition);
    }

    /**
     * Upgrade customer password hash when customer has logged in
     *
     * @param Varien_Event_Observer $observer
     */
    public function actionUpgradeCustomerPassword($observer)
    {
        $password = $observer->getEvent()->getPassword();
        $model = $observer->getEvent()->getModel();

        $encryptor = Mage::helper('core')->getEncryptor();
        $hashVersionArray = [
            Mage_Core_Model_Encryption::HASH_VERSION_MD5,
            Mage_Core_Model_Encryption::HASH_VERSION_SHA256,
            Mage_Core_Model_Encryption::HASH_VERSION_SHA512,
            Mage_Core_Model_Encryption::HASH_VERSION_LATEST,
        ];
        $currentVersionHash = null;
        foreach ($hashVersionArray as $hashVersion) {
            if ($encryptor->validateHashByVersion($password, $model->getPasswordHash(), $hashVersion)) {
                $currentVersionHash = $hashVersion;
                break;
            }
        }
        if (Mage_Core_Model_Encryption::HASH_VERSION_SHA256 !== $currentVersionHash) {
            $model->changePassword($password, false);
        }
    }
}
