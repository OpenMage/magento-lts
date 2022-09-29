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
 * @package    Mage_GiftMessage
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_GiftMessage
 * @author     Magento Core Team <core@magentocommerce.com>
 * @deprecated after 1.3.2.4
 *
 * @method $this setCanDisplayContainer(bool $value)
 */
class Mage_GiftMessage_Block_Message_Helper extends Mage_Core_Block_Template
{
    protected $_entity = null;
    protected $_type   = null;
    protected $_giftMessage = null;

    static protected $_scriptIncluded = false;

    /**
     * Mage_GiftMessage_Block_Message_Helper constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('giftmessage/helper.phtml');
    }

    /**
     * @param mixed $entity
     * @return $this
     */
    public function setEntity($entity)
    {
        $this->_entity = $entity;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEntity()
    {
        return $this->_entity;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        $this->_type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * @return bool
     */
    public function hasGiftMessage()
    {
        return $this->getEntity()->getGiftMessageId() > 0;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setScriptIncluded($value)
    {
        self::$_scriptIncluded = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getScriptIncluded()
    {
        return self::$_scriptIncluded;
    }

    /**
     * @return string
     */
    public function getJsObjectName()
    {
        return $this->getId() . 'JsObject';
    }

    /**
     * @return string
     */
    public function getEditUrl()
    {
        /** @var Mage_GiftMessage_Helper_Url $helper */
        $helper = $this->helper('giftmessage/url');
        return $helper->getEditUrl($this->getEntity(), $this->getType());
    }

    /**
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
     * @return Mage_GiftMessage_Model_Message
     */
    public function getMessage()
    {
        if (is_null($this->_giftMessage)) {
            $this->_initMessage();
        }

        return $this->_giftMessage;
    }
}
