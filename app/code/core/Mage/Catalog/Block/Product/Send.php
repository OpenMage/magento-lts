<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product send to friend block
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
class Mage_Catalog_Block_Product_Send extends Mage_Catalog_Block_Product_Abstract
{
    /**
     * Retrieve username for form field
     *
     * @return string
     */

    public function getUserName()
    {
        return Mage::getSingleton('customer/session')->getCustomer()->getName();
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return (string) Mage::getSingleton('customer/session')->getCustomer()->getEmail();
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getProductId()
    {
        return $this->getRequest()->getParam('id');
    }

    /**
     * @return int
     */
    public function getMaxRecipients()
    {
        $sendToFriendModel = Mage::registry('send_to_friend_model');
        return $sendToFriendModel->getMaxRecipients();
    }
}
