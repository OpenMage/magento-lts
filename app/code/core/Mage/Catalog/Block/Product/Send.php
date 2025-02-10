<?php
/**
 * Product send to friend block
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
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
