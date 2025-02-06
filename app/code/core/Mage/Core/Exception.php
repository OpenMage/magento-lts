<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Core
 */

/**
 * Magento Core Exception
 *
 * This class will be extended by other modules
 *
 * @category   Mage
 * @package    Mage_Core
 */
class Mage_Core_Exception extends Exception
{
    protected $_messages = [];

    /**
     * @return $this
     */
    public function addMessage(Mage_Core_Model_Message_Abstract $message)
    {
        if (!isset($this->_messages[$message->getType()])) {
            $this->_messages[$message->getType()] = [];
        }
        $this->_messages[$message->getType()][] = $message;
        return $this;
    }

    /**
     * @param string $type
     * @return array|Mage_Core_Model_Message_Abstract[]
     */
    public function getMessages($type = '')
    {
        if ($type == '') {
            $arrRes = [];
            foreach ($this->_messages as $messageType => $messages) {
                $arrRes = array_merge($arrRes, $messages);
            }
            return $arrRes;
        }
        return $this->_messages[$type] ?? [];
    }

    /**
     * Set or append a message to existing one
     *
     * @param string $message
     * @param bool $append
     * @return $this
     */
    public function setMessage($message, $append = false)
    {
        if ($append) {
            $this->message .= $message;
        } else {
            $this->message = $message;
        }
        return $this;
    }
}
