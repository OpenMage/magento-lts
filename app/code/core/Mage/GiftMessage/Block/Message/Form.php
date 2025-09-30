<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_GiftMessage
 */

/**
 * @package    Mage_GiftMessage
 * @deprecated after 1.3.2.4
 */
class Mage_GiftMessage_Block_Message_Form extends Mage_Core_Block_Template
{
    /**
     * @var Mage_GiftMessage_Model_Message|null
     */
    protected $_giftMessage = null;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('giftmessage/form.phtml');
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getSaveUrl()
    {
        /** @var Mage_GiftMessage_Helper_Url $helper */
        $helper = $this->helper('giftmessage/url');
        return $helper->getSaveUrl(
            $this->getRequest()->getParam('item'),
            $this->getRequest()->getParam('type'),
            $this->getRequest()->getParam('message'),
            ['uniqueId' => $this->getRequest()->getParam('uniqueId')],
        );
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getEditUrl()
    {
        /** @var Mage_GiftMessage_Helper_Url $helper */
        $helper = $this->helper('giftmessage/url');
        return $helper->getEditUrl(
            $this->getRequest()->getParam('entity'),
            $this->getRequest()->getParam('type'),
        );
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getButtonUrl()
    {
        /** @var Mage_GiftMessage_Helper_Url $helper */
        $helper = $this->helper('giftmessage/url');
        return $helper->getButtonUrl(
            $this->getRequest()->getParam('item'),
            $this->getRequest()->getParam('type'),
        );
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getRemoveUrl()
    {
        /** @var Mage_GiftMessage_Helper_Url $helper */
        $helper = $this->helper('giftmessage/url');
        return $helper->getRemoveUrl(
            $this->getRequest()->getParam('item'),
            $this->getRequest()->getParam('type'),
            ['uniqueId' => $this->getRequest()->getParam('uniqueId')],
        );
    }

    /**
     * @return $this
     * @throws Exception
     */
    protected function _initMessage()
    {
        /** @var Mage_GiftMessage_Helper_Message $helper */
        $helper = $this->helper('giftmessage/message');
        $this->_giftMessage = $helper->getGiftMessage($this->getRequest()->getParam('message'));
        return $this;
    }

    /**
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
     * @param string $value
     * @return string
     */
    public function getEscaped($value)
    {
        return $this->escapeHtml($value);
    }

    /**
     * @param string $value
     * @return string
     */
    public function getEscapedForJs($value)
    {
        return addcslashes($value, "\\'\n\r\t");
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getUniqueId()
    {
        return $this->getRequest()->getParam('uniqueId');
    }
}
