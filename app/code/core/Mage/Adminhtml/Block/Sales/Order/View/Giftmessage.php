<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Edit order giftmessage block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Sales_Order_View_Giftmessage extends Mage_Adminhtml_Block_Widget
{
    /**
     * Entity for editing of gift message
     *
     * @var Mage_Eav_Model_Entity_Abstract
     */
    protected $_entity;

    /**
     * Retrieve order model instance
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return Mage::registry('current_order');
    }

    /**
     * Giftmessage object
     *
     * @var Mage_GiftMessage_Model_Message
     */
    protected $_giftMessage;

    /**
     * @inheritDoc
     */
    protected function _beforeToHtml()
    {
        if ($this->getParentBlock() && ($order = $this->getOrder())) {
            $this->setEntity($order);
        }
        return parent::_beforeToHtml();
    }

    /**
     * Prepares layout of block
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->setChild(
            'save_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label'   => Mage::helper('giftmessage')->__('Save Gift Message'),
                    'class'   => 'save'
                ])
        );

        return $this;
    }

    /**
     * Retrieve save button html
     *
     * @return string
     * @throws Exception
     */
    public function getSaveButtonHtml()
    {
        $this->getChild('save_button')->setOnclick(
            'giftMessagesController.saveGiftMessage(\''. $this->getHtmlId() .'\')'
        );

        return $this->getChildHtml('save_button');
    }

    /**
     * Set entity for form
     *
     * @param Varien_Object $entity
     * @return $this
     */
    public function setEntity(Varien_Object $entity)
    {
        $this->_entity  = $entity;
        return $this;
    }

    /**
     * Retrieve entity for form
     *
     * @return Varien_Object
     * @throws Exception
     */
    public function getEntity()
    {
        if (is_null($this->_entity)) {
            $this->setEntity(Mage::getModel('giftmessage/message')->getEntityModelByType('order'));
            $this->getEntity()->load($this->getRequest()->getParam('entity'));
        }
        return $this->_entity;
    }

    /**
     * Retrieve default value for giftmessage sender
     *
     * @return string
     * @throws Exception
     */
    public function getDefaultSender()
    {
        if (!$this->getEntity()) {
            return '';
        }

        if ($this->getEntity()->getOrder()) {
            return $this->getEntity()->getOrder()->getCustomerName();
        }

        return $this->getEntity()->getCustomerName();
    }

    /**
     * Retrieve default value for giftmessage recipient
     *
     * @return string
     * @throws Exception
     */
    public function getDefaultRecipient()
    {
        if (!$this->getEntity()) {
            return '';
        }

        if ($this->getEntity()->getOrder()) {
            if ($this->getEntity()->getOrder()->getShippingAddress()) {
                return $this->getEntity()->getOrder()->getShippingAddress()->getName();
            }

            if ($this->getEntity()->getOrder()->getBillingAddress()) {
                return $this->getEntity()->getOrder()->getBillingAddress()->getName();
            }
        }

        if ($this->getEntity()->getShippingAddress()) {
            return $this->getEntity()->getShippingAddress()->getName();
        }

        if ($this->getEntity()->getBillingAddress()) {
            return $this->getEntity()->getBillingAddress()->getName();
        }

        return '';
    }

    /**
     * Retrieve real name for field
     *
     * @param string $name
     * @return string
     * @throws Exception
     */
    public function getFieldName($name)
    {
        return 'giftmessage[' . $this->getEntity()->getId() . '][' . $name . ']';
    }

    /**
     * Retrieve real html id for field
     *
     * @param string $id
     * @return string
     * @throws Exception
     */
    public function getFieldId($id)
    {
        return $this->getFieldIdPrefix() . $id;
    }

    /**
     * Retrieve field html id prefix
     *
     * @return string
     * @throws Exception
     */
    public function getFieldIdPrefix()
    {
        return 'giftmessage_order_' . $this->getEntity()->getId() . '_';
    }

    /**
     * Initialize gift message for entity
     *
     * @return $this
     * @throws Exception
     */
    protected function _initMessage()
    {
        /** @var Mage_GiftMessage_Helper_Message $helper */
        $helper = $this->helper('giftmessage/message');
        $this->_giftMessage = $helper->getGiftMessage($this->getEntity()->getGiftMessageId());

        // init default values for giftmessage form
        if (!$this->getMessage()->getSender()) {
            $this->getMessage()->setSender($this->getDefaultSender());
        }
        if (!$this->getMessage()->getRecipient()) {
            $this->getMessage()->setRecipient($this->getDefaultRecipient());
        }

        return $this;
    }

    /**
     * Retrieve gift message for entity
     *
     * @return Mage_GiftMessage_Model_Message
     * @throws Exception
     */
    public function getMessage()
    {
        if (is_null($this->_giftMessage)) {
            $this->_initMessage();
        }

        return $this->_giftMessage;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getSaveUrl()
    {
        return $this->getUrl(
            '*/sales_order_view_giftmessage/save',
            [
                'entity'=>$this->getEntity()->getId(),
                'type'  =>'order',
                'reload' => 1
            ]
        );
    }

    /**
     * Retrieve block html id
     *
     * @return string
     * @throws Exception
     */
    public function getHtmlId()
    {
        return substr($this->getFieldIdPrefix(), 0, -1);
    }

    /**
     * Indicates that block can display giftmessages form
     *
     * @return boolean
     * @throws Exception
     */
    public function canDisplayGiftmessage()
    {
        /** @var Mage_GiftMessage_Helper_Message $helper */
        $helper = $this->helper('giftmessage/message');
        return $helper->getIsMessagesAvailable('order', $this->getEntity(), $this->getEntity()->getStoreId());
    }
}
