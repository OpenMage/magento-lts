<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Rss
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2021-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Class Mage_Rss_Block_Abstract
 *
 * @category   Mage
 * @package    Mage_Rss
 *
 * @method int getStoreId()
 */
class Mage_Rss_Block_Abstract extends Mage_Core_Block_Template
{
    /**
     * @return int
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function _getStoreId()
    {
        //store id is store view id
        $storeId =   (int) $this->getRequest()->getParam('store_id');
        if ($storeId == null) {
            $storeId = Mage::app()->getStore()->getId();
        }
        return $storeId;
    }

    /**
     * @return int
     * @throws Exception
     */
    protected function _getCustomerGroupId()
    {
        //customer group id
        $custGroupID =   (int) $this->getRequest()->getParam('cid');
        if ($custGroupID == null) {
            $custGroupID = Mage::getSingleton('customer/session')->getCustomerGroupId();
        }
        return $custGroupID;
    }
}
