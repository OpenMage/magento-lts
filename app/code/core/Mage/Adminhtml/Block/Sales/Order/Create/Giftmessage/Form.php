<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml order creating gift message item form
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_Create_Giftmessage_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Entity for editing of gift message
     *
     * @var Varien_Object
     */
    protected $_entity;

    /**
     * Giftmessage object
     *
     * @var Mage_GiftMessage_Model_Message|null
     */
    protected $_giftMessage;

    /**
     * Set entity for form
     *
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
     */
    public function getEntity()
    {
        return $this->_entity;
    }

    /**
     * @return Mage_Adminhtml_Model_Session_Quote
     */
    protected function _getSession()
    {
        return Mage::getSingleton('adminhtml/session_quote');
    }

    /**
     * Retrieve default value for giftmessage sender
     *
     * @return string
     */
    public function getDefaultSender()
    {
        if (!$this->getEntity()) {
            return '';
        }

        if ($this->_getSession()->getCustomer()->getId()) {
            return $this->_getSession()->getCustomer()->getName();
        }

        $object = $this->getEntity();

        if ($this->getEntity()->getQuote()) {
            $object = $this->getEntity()->getQuote();
        }

        return $object->getBillingAddress()->getName();
    }

    /**
     * Retrieve default value for giftmessage recipient
     *
     * @return string
     */
    public function getDefaultRecipient()
    {
        if (!$this->getEntity()) {
            return '';
        }

        $object = $this->getEntity();

        if ($this->getEntity()->getOrder()) {
            $object = $this->getEntity()->getOrder();
        } elseif ($this->getEntity()->getQuote()) {
            $object = $this->getEntity()->getQuote();
        }

        if ($object->getShippingAddress()) {
            return $object->getShippingAddress()->getName();
        }

        if ($object->getBillingAddress()) {
            return $object->getBillingAddress()->getName();
        }

        return '';
    }

    /**
     * Prepares form
     *
     * @return $this
     * @throws Exception
     */
    public function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('main', ['no_container' => true]);

        $fieldset->addField(
            'type',
            'hidden',
            [
                'name' =>  $this->_getFieldName('type'),
            ],
        );

        $form->setHtmlIdPrefix($this->_getFieldIdPrefix());

        if ($this->getEntityType() === 'item') {
            $this->_prepareHiddenFields($fieldset);
        } else {
            $this->_prepareVisibleFields($fieldset);
        }

        // Set default sender and recipient from billing and shipping addresses
        if (!$this->getMessage()->getSender()) {
            $this->getMessage()->setSender($this->getDefaultSender());
        }

        if (!$this->getMessage()->getRecipient()) {
            $this->getMessage()->setRecipient($this->getDefaultRecipient());
        }

        $this->getMessage()->setType($this->getEntityType());

        // Overridden default data with edited when block reloads through Ajax
        $this->_applyPostData();

        $form->setValues($this->getMessage()->getData());

        $this->setForm($form);
        return $this;
    }

    /**
     * Prepare form fieldset
     * All fields are hidden
     *
     *
     * @return $this
     */
    protected function _prepareHiddenFields(Varien_Data_Form_Element_Fieldset $fieldset)
    {
        $fieldset->addField(
            'sender',
            'hidden',
            [
                'name' => $this->_getFieldName('sender'),
            ],
        );
        $fieldset->addField(
            'recipient',
            'hidden',
            [
                'name' => $this->_getFieldName('recipient'),
            ],
        );

        $fieldset->addField(
            'message',
            'hidden',
            [
                'name' => $this->_getFieldName('message'),
            ],
        );
        return $this;
    }

    /**
     * Prepare form fieldset
     * All fields are visible
     *
     *
     * @return $this
     */
    protected function _prepareVisibleFields(Varien_Data_Form_Element_Fieldset $fieldset)
    {
        $fieldset->addField(
            'sender',
            'text',
            [
                'name'     => $this->_getFieldName('sender'),
                'label'    => Mage::helper('sales')->__('From'),
                'required' => $this->getMessage()->getMessage() ? true : false,
            ],
        );
        $fieldset->addField(
            'recipient',
            'text',
            [
                'name'     => $this->_getFieldName('recipient'),
                'label'    => Mage::helper('sales')->__('To'),
                'required' => $this->getMessage()->getMessage() ? true : false,
            ],
        );

        $fieldset->addField(
            'message',
            'textarea',
            [
                'name'      => $this->_getFieldName('message'),
                'label'     => Mage::helper('sales')->__('Message'),
                'rows'      => '5',
                'cols'      => '20',
            ],
        );
        return $this;
    }

    /**
     * Initialize gift message for entity
     *
     * @return $this
     */
    protected function _initMessage()
    {
        /** @var Mage_GiftMessage_Helper_Message $helper */
        $helper = $this->helper('giftmessage/message');
        $this->_giftMessage = $helper->getGiftMessage($this->getEntity()->getGiftMessageId());
        return $this;
    }

    /**
     * Retrieve gift message for entity
     *
     * @return Mage_GiftMessage_Model_Message
     */
    public function getMessage()
    {
        if (is_null($this->_giftMessage)) {
            $this->_initMessage();
        }

        return $this->_giftMessage;
    }

    /**
     * Retrieve real name for field
     *
     * @param string $name
     * @return string
     */
    protected function _getFieldName($name)
    {
        return 'giftmessage[' . $this->getEntity()->getId() . '][' . $name . ']';
    }

    /**
     * Retrieve real html id for field
     *
     * @param string $id
     * @return string
     */
    protected function _getFieldId($id)
    {
        return $this->_getFieldIdPrefix() . $id;
    }

    /**
     * Retrieve field html id prefix
     *
     * @return string
     */
    protected function _getFieldIdPrefix()
    {
        return 'giftmessage_' . $this->getEntity()->getId() . '_';
    }

    /**
     * Aplies posted data to gift message
     *
     * @return $this
     * @throws Exception
     */
    protected function _applyPostData()
    {
        if (is_array($giftmessages = $this->getRequest()->getParam('giftmessage'))
            && isset($giftmessages[$this->getEntity()->getId()])
        ) {
            $this->getMessage()->addData($giftmessages[$this->getEntity()->getId()]);
        }

        return $this;
    }
}
