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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * XmlConnect message abstract class
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_XmlConnect_Model_Simplexml_Message_Abstract extends Varien_Object
{
    /**
     * Simple Xml object
     *
     * @var Mage_XmlConnect_Model_Simplexml
     */
    protected $_xmlObject;

    /**
     * Message model
     *
     * @var Mage_XmlConnect_Model_Simplexml_Message
     */
    protected $_messageModel;

    /**
     * Init simple xml message
     *
     * @param Mage_XmlConnect_Model_Simplexml_Message $messageModel
     */
    public function __construct($messageModel)
    {
        $this->_xmlObject = Mage::getModel('xmlconnect/simplexml_element', '<message></message>');
        $this->_messageModel = $messageModel;
    }

    /**
     * Get message xml
     *
     * @abstract
     * @return string
     */
    abstract public function getMessage();

    /**
     * Get simple xml message object
     *
     * @return Mage_XmlConnect_Model_Simplexml_Element
     */
    protected function _getXmlObject()
    {
        return $this->_xmlObject;
    }

    /**
     * Get message code
     *
     * @return string
     */
    protected function _getMessageCode()
    {
        return $this->_messageModel->getMessageCode();
    }

    /**
     * Set message code
     *
     * @param string $messageCode
     * @return Mage_XmlConnect_Model_Simplexml_Message_Abstract
     */
    protected function _setMessageCode($messageCode)
    {
        $this->_messageModel->setMessageCode($messageCode);
        return $this;
    }

    /**
     * Get message text
     *
     * @return null|string
     */
    protected function _getMessageText()
    {
        return $this->_messageModel->getMessageText();
    }

    /**
     * Set message text
     *
     * @param string $text
     * @return Mage_XmlConnect_Model_Simplexml_Message_Abstract
     */
    protected function _setMessageText($text)
    {
        $this->_messageModel->setMessageText($text);
        return $this;
    }

    /**
     * Get message status
     *
     * @return string
     */
    protected function _getMessageStatus()
    {
        return $this->_messageModel->getMessageStatus();
    }

    /**
     * Get message children
     *
     * @return array
     */
    protected function _getChildren()
    {
        return $this->_messageModel->getChildren();
    }
}
