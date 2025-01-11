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
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * API2 class for customer (admin)
 *
 * @category   Mage
 * @package    Mage_Customer
 */
class Mage_Customer_Model_Api2_Customer_Rest_Admin_V1 extends Mage_Customer_Model_Api2_Customer_Rest
{
    /**
     * Retrieve information about customer
     * Add last logged in datetime
     *
     * @throws Mage_Api2_Exception
     * @return array
     */
    protected function _retrieve()
    {
        /** @var Mage_Log_Model_Customer $log */
        $log = Mage::getModel('log/customer');
        $log->loadByCustomer($this->getRequest()->getParam('id'));

        $data = parent::_retrieve();
        $data['is_confirmed'] = (int) !(isset($data['confirmation']) && $data['confirmation']);

        $lastLoginAt = $log->getLoginAt();
        if ($lastLoginAt !== null) {
            $data['last_logged_in'] = $lastLoginAt;
        }
        return $data;
    }

    /**
     * Delete customer
     */
    protected function _delete()
    {
        $customer = parent::_loadCustomerById($this->getRequest()->getParam('id'));

        try {
            $customer->delete();
        } catch (Mage_Core_Exception $e) {
            $this->_critical($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_critical(self::RESOURCE_INTERNAL_ERROR);
        }
    }
}
