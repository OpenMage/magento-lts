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
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * XmlConnect simple message class
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Model_Simplexml_Message extends Varien_Object
{
    /**
     * Message status `error`
     */
    const MESSAGE_STATUS_ERROR      = 'error';

    /**
     * Message status `warning`
     */
    const MESSAGE_STATUS_WARNING    = 'warning';

    /**
     * Message status `success`
     */
    const MESSAGE_STATUS_SUCCESS    = 'success';

    /**
     * Error message decorator
     *
     * @var Mage_XmlConnect_Model_Simplexml_Message_Abstract
     */
    protected $_messageRenderer;

    /**
     * Message status
     *
     * @var string
     */
    protected $_messageStatus;

    /**
     * Message code
     *
     * @var string
     */
    protected $_messageCode;

    /**
     * Message text
     *
     * @var string|null
     */
    protected $_messageText;

    /**
     * Children message params
     *
     * @var array
     */
    protected $_children = array();

    /**
     * Flag error in processing the message
     *
     * @var bool
     */
    protected $_flagError = false;

    /**
     * Init simple xml message
     *
     * @param string $messageCode
     */
    public function __construct($messageCode)
    {
        $this->setMessageCode($messageCode)->_setMessageStatus();

        switch ($this->getMessageStatus()) {
            case self::MESSAGE_STATUS_SUCCESS:
            case self::MESSAGE_STATUS_WARNING:
                $renderer = self::MESSAGE_STATUS_SUCCESS;
                break;
            case self::MESSAGE_STATUS_ERROR:
                $renderer = self::MESSAGE_STATUS_ERROR;
                break;
            default:
                $this->setMessageCode(Mage_XmlConnect_Model_Simplexml_Message_Error::ERROR_SERVER_SP_DEFAULT)
                    ->setMessageText(Mage::helper('xmlconnect')->__('Message type doesn\'t recognized'))
                    ->_setMessageStatus()->_setFlagError(true);
                $renderer = self::MESSAGE_STATUS_ERROR;
                break;
        }
        $this->_setMessageRenderer(Mage::getModel('xmlconnect/simplexml_message_' . $renderer, $this));
    }

    /**
     * Get message type
     *
     * @return Mage_XmlConnect_Model_Simplexml_Message
     */
    protected function _setMessageStatus()
    {
        list($messageType) = explode('_', $this->_messageCode);
        $this->_messageStatus = $messageType;
        return $this;
    }

    /**
     * Get message status
     *
     * @return string
     */
    public function getMessageStatus()
    {
        return $this->_messageStatus;
    }

    /**
     * Get message text
     *
     * @return string|null
     */
    public function getMessageText()
    {
        return $this->_messageText;
    }

    /**
     * set message text
     *
     * @param string $text
     * @return Mage_XmlConnect_Model_Simplexml_Message
     */
    public function setMessageText($text)
    {
        if (!$this->_getFlagError()) {
            $this->_messageText = $text;
        }
        return $this;
    }

    /**
     * Get message code
     *
     * @return string
     */
    public function getMessageCode()
    {
        return $this->_messageCode;
    }

    /**
     * Set message code
     *
     * @param string $messageCode
     * @return Mage_XmlConnect_Model_Simplexml_Message
     */
    public function setMessageCode($messageCode)
    {
        if (!$this->_getFlagError()) {
            $this->_messageCode = $messageCode;
        }
        return $this;
    }

    /**
     * Convert object attributes to string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->_getMessageRenderer()->getMessage();
    }

    /**
     * Set children message params
     *
     * @param array $children
     * @return Mage_XmlConnect_Model_Simplexml_Message
     */
    public function setChildren($children)
    {
        if (!$this->_getFlagError()) {
            $this->_children = $children;
        }
        return $this;
    }

    /**
     * Set children message params
     *
     * @return array
     */
    public function getChildren()
    {
        return $this->_children;
    }

    /**
     * Get message renderer
     *
     * @return Mage_XmlConnect_Model_Simplexml_Message_Abstract
     */
    protected function _getMessageRenderer()
    {
        return $this->_messageRenderer;
    }

    /**
     * Set message renderer
     *
     * @param Mage_XmlConnect_Model_Simplexml_Message_Abstract $messageRenderer
     * @return Mage_XmlConnect_Model_Simplexml_Message
     */
    protected function _setMessageRenderer($messageRenderer)
    {
        $this->_messageRenderer = $messageRenderer;
        return $this;
    }

    /**
     * Get flag error in processing
     *
     * @return boolean
     */
    public function _getFlagError()
    {
        return $this->_flagError;
    }

    /**
     * Set flag error in processing
     *
     * @param boolean $flagError
     * @return Mage_XmlConnect_Model_Simplexml_Message
     */
    public function _setFlagError($flagError)
    {
        $this->_flagError = $flagError;
        return $this;
    }
}
