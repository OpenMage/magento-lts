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
 * @package    Mage_GiftMessage
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Gift message inline edit form
 *
 * @category   Mage
 * @package    Mage_GiftMessage
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_GiftMessage_Block_Message_Inline extends Mage_Core_Block_Template
{

    protected $_entity = null;
    protected $_type   = null;
    protected $_giftMessage = null;

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('giftmessage/inline.phtml');
    }

    public function setEntity($entity)
    {
        $this->_entity = $entity;
        return $this;
    }

    public function getEntity()
    {
        return $this->_entity;
    }

    public function setType($type)
    {
        $this->_type = $type;
        return $this;
    }

    public function getType()
    {
        return $this->_type;
    }

    public function hasGiftMessage()
    {
        return $this->getEntity()->getGiftMessageId() > 0;
    }

    protected function _initMessage()
    {
        $this->_giftMessage = $this->helper('giftmessage/message')->getGiftMessage(
                                            $this->getEntity()->getGiftMessageId()
                              );
        return $this;
    }

    public function getDefaultFrom()
    {
        return Mage::getSingleton('customer/session')->isLoggedIn() ? Mage::getSingleton('customer/session')->getCustomer()->getName() :  $this->getEntity()->getBillingAddress()->getName();
    }

    public function getDefaultTo()
    {
        return $this->getEntity()->getShippingAddress() ? $this->getEntity()->getShippingAddress()->getName() : $this->getEntity()->getName();
    }

    public function getMessage($entity=null)
    {
        if(is_null($this->_giftMessage)) {
            $this->_initMessage();
        }

        if($entity) {
            if(!$entity->getGiftMessage()) {
                $entity->setGiftMessage($this->helper('giftmessage/message')->getGiftMessage($entity->getGiftMessageId()));
            }
            return $entity->getGiftMessage();
        }

        return $this->_giftMessage;
    }

    public function getItems()
    {
        if(!$this->getData('items')) {
            $items = array();
            foreach ($this->getEntity()->getAllItems() as $item) {
                if ($item->getParentItem()) {
                    continue;
                }
                if($this->helper('giftmessage/message')->isMessagesAvailable( substr($this->getType(), 0, 5)=='multi' ? 'address_item'  : 'item', $item)) {
                    $items[] = $item;
                }
            }
            $this->setData('items', $items);
        }

        return $this->getData('items');
    }

    public function getAdditionalUrl()
    {
        return $this->getUrl('*/*/getAdditional');
    }

    public function isItemsAvailable()
    {
        return count($this->getItems()) > 0;
    }

    public function countItems()
    {
        return count($this->getItems());
    }

    public function getItemsHasMesssages()
    {
        foreach($this->getItems() as $item) {
            if($item->getGiftMessageId()) {
                return true;
            }
        }

        return false;
    }

    public function getEntityHasMessage()
    {
        return $this->getEntity()->getGiftMessageId() > 0;
    }

    public function getEscaped($value, $defaultValue='')
    {
        return $this->htmlEscape(trim($value)!='' ? $value : $defaultValue);
    }

}
