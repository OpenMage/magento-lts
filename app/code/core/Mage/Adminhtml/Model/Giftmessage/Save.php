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
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Adminhtml giftmessage save model
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Model_Giftmessage_Save extends Varien_Object
{
    protected $_saved = false;

    /**
     * Save all seted giftmessages
     *
     * @return Mage_Adminhtml_Model_Giftmessage_Save
     */
    public function saveAllInQuote()
    {
        $giftmessages = $this->getGiftmessages();

        // remove disabled giftmessages
        foreach ($this->_getQuote()->getAllItems() as $item) {
            if($item->getGiftMessageId() && !in_array($item->getId(), $this->getAllowQuoteItems())) {
                $this->_deleteOne($item);
            }
        }

        if (!is_array($giftmessages)) {
            return $this;
        }

        foreach ($giftmessages as $entityId=>$giftmessage) {
            $this->_saveOne($entityId, $giftmessage);
        }

        return $this;
    }

    public function getSaved()
    {
        return $this->_saved;
    }

    public function saveAllInOrder()
    {
        $giftmessages = $this->getGiftmessages();

        if (!is_array($giftmessages)) {
            return $this;
        }

        foreach ($giftmessages as $entityId=>$giftmessage) {
            $this->_saveOne($entityId, $giftmessage);
        }

        return $this;
    }

    /**
     * Save a single gift message
     *
     * @param integer $entityId
     * @param array $giftmessage
     * @return Mage_Adminhtml_Model_Giftmessage_Save
     */
    protected function _saveOne($entityId, $giftmessage) {
        $giftmessageModel = Mage::getModel('giftmessage/message');

        if ($this->_getMappedType($giftmessage['type'])!='quote_item') {
            $entityModel = $giftmessageModel->getEntityModelByType($this->_getMappedType($giftmessage['type']));
        } else {
            $entityModel = $this->_getQuote()->getItemById($entityId);
        }



        if ($this->_getMappedType($giftmessage['type'])=='quote') {
            $entityModel->setStoreId($this->_getQuote()->getStoreId());
        }

        if ($this->_getMappedType($giftmessage['type'])!='quote_item') {
            $entityModel->load($entityId);
        }


        if ($entityModel->getGiftMessageId()) {
            $giftmessageModel->load($entityModel->getGiftMessageId());
        }

        $giftmessageModel->addData($giftmessage);

        if ($giftmessageModel->isMessageEmpty() && $giftmessageModel->getId()) {
            // remove empty giftmessage
            $this->_deleteOne($entityModel, $giftmessageModel);
            $this->_saved = false;
        } elseif (!$giftmessageModel->isMessageEmpty()) {
            $giftmessageModel->save();
            $entityModel->setGiftMessageId($giftmessageModel->getId())
                ->save();
            $this->_saved = true;
        }

        return $this;
    }

    /**
     * Delete a single gift message from entity
     *
     * @param Mage_GiftMessage_Model_Message|null $giftmessageModel
     * @param Varien_Object $entityModel
     * @return Mage_Adminhtml_Model_Giftmessage_Save
     */
    protected function _deleteOne($entityModel, $giftmessageModel=null)
    {
        if(is_null($giftmessageModel)) {
            $giftmessageModel = Mage::getModel('giftmessage/message')
                ->load($entityModel->getGiftMessageId());
        }
        $giftmessageModel->delete();
        $entityModel->setGiftMessageId(0)
            ->save();
        return $this;
    }

    /**
     * Set allowed quote items for gift messages
     *
     * @param array $items
     * @return Mage_Adminhtml_Model_Giftmessage_Save
     */
    public function setAllowQuoteItems($items)
    {
        $this->_getSession()->setAllowQuoteItemsGiftMessage($items);
        return $this;
    }

    /**
     * Retrive allowed quote items for gift messages
     *
     * @return array
     */
    public function getAllowQuoteItems()
    {
        if(!is_array($this->_getSession()->getAllowQuoteItemsGiftMessage())) {
            $this->setAllowQuoteItems(array());
        }

        return $this->_getSession()->getAllowQuoteItemsGiftMessage();
    }

    /**
     * Retrive allowed quote items products for gift messages
     *
     * @return array
     */
    public function getAllowQuoteItemsProducts()
    {
        $result = array();
        foreach ($this->getAllowQuoteItems() as $itemId) {
            $item = $this->_getQuote()->getItemById($itemId);
            if(!$item) {
                continue;
            }
            $result[] = $item->getProduct()->getId();
        }
        return $result;
    }

    /**
     * Checks allowed quote item for gift messages
     *
     * @param  Varien_Object $item
     * @return boolean
     */
    public function getIsAllowedQuoteItem($item)
    {
        if(!in_array($item->getId(), $this->getAllowQuoteItems())) {
            return false;
        }

        return true;
    }


    /**
     * Imports quote items for gift messages from products data
     *
     * @param unknown_type $products
     * @return unknown
     */
    public function importAllowQuoteItemsFromProducts($products)
    {
        $allowedItems = $this->getAllowQuoteItems();
        $deleteAllowedItems = array();
        foreach ($products as $productId=>$data) {
            $product = Mage::getModel('catalog/product')
                ->setStore($this->_getSession()->getStore())
                ->load($productId);
            $item = $this->_getQuote()->getItemByProduct($product);

            if(!$item) {
                continue;
            }

            if (in_array($item->getId(), $allowedItems)
                && !isset($data['giftmessage'])) {
                $deleteAllowedItems[] = $item->getId();
            } elseif (!in_array($item->getId(), $allowedItems)
                      && isset($data['giftmessage'])) {
                $allowedItems[] = $item->getId();
            }

        }

        $allowedItems = array_diff($allowedItems, $deleteAllowedItems);

        $this->setAllowQuoteItems($allowedItems);
        return $this;
    }

    public function importAllowQuoteItemsFromItems($items)
    {
        $allowedItems = $this->getAllowQuoteItems();
        $deleteAllowedItems = array();
        foreach ($items as $itemId=>$data) {

            $item = $this->_getQuote()->getItemById($itemId);

            if(!$item) {
                // Clean not exists items
                $deleteAllowedItems[] = $itemId;
                continue;
            }

            if (in_array($item->getId(), $allowedItems)
                && !isset($data['giftmessage'])) {
                $deleteAllowedItems[] = $item->getId();
            } elseif (!in_array($item->getId(), $allowedItems)
                      && isset($data['giftmessage'])) {
                $allowedItems[] = $item->getId();
            }

        }

        $allowedItems = array_diff($allowedItems, $deleteAllowedItems);
        $this->setAllowQuoteItems($allowedItems);
        return $this;
    }

    /**
     * Retrive mapped type for entity
     *
     * @param string $type
     * @return string
     */
    protected function _getMappedType($type)
    {
        $map = array(
            'main'          =>  'quote',
            'item'          =>  'quote_item',
            'order'         =>  'order',
            'order_item'    =>  'order_item'
        );

        if (isset($map[$type])) {
            return $map[$type];
        }

        return null;
    }

    /**
     * Retrieve session object
     *
     * @return Mage_Adminhtml_Model_Session_Quote
     */
    protected function _getSession()
    {
        return Mage::getSingleton('adminhtml/session_quote');
    }

    /**
     * Retrieve quote object
     *
     * @return Mage_Sales_Model_Quote
     */
    protected function _getQuote()
    {
        return $this->_getSession()->getQuote();
    }

}
