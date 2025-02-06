<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Catalog
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
