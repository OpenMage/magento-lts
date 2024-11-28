<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Messages collection
 *
 * @category   Mage
 * @package    Mage_Core
 */
class Mage_Core_Model_Message_Collection
{
    /**
     * All messages by type array
     *
     * @var array
     */
    protected $_messages = [];
    protected $_lastAddedMessage;

    /**
     * Adding new message to collection
     *
     * @return Mage_Core_Model_Message_Collection
     */
    public function add(Mage_Core_Model_Message_Abstract $message)
    {
        return $this->addMessage($message);
    }

    /**
     * Adding new message to collection
     *
     * @return  Mage_Core_Model_Message_Collection
     */
    public function addMessage(Mage_Core_Model_Message_Abstract $message)
    {
        if (!isset($this->_messages[$message->getType()])) {
            $this->_messages[$message->getType()] = [];
        }
        $this->_messages[$message->getType()][] = $message;
        $this->_lastAddedMessage = $message;
        return $this;
    }

    /**
     * Clear all messages except sticky
     *
     * @return $this
     */
    public function clear()
    {
        foreach ($this->_messages as $type => $messages) {
            foreach ($messages as $id => $message) {
                if (!$message->getIsSticky()) {
                    unset($this->_messages[$type][$id]);
                }
            }
            if (empty($this->_messages[$type])) {
                unset($this->_messages[$type]);
            }
        }
        return $this;
    }

    /**
     * Get last added message if any
     *
     * @return Mage_Core_Model_Message_Abstract|null
     */
    public function getLastAddedMessage()
    {
        return $this->_lastAddedMessage;
    }

    /**
     * Get first even message by identifier
     *
     * @param string $identifier
     * @return Mage_Core_Model_Message_Abstract|void
     */
    public function getMessageByIdentifier($identifier)
    {
        foreach ($this->_messages as $messages) {
            foreach ($messages as $message) {
                if ($identifier === $message->getIdentifier()) {
                    return $message;
                }
            }
        }
    }

    /**
     * @param string $identifier
     */
    public function deleteMessageByIdentifier($identifier)
    {
        foreach ($this->_messages as $type => $messages) {
            foreach ($messages as $id => $message) {
                if ($identifier === $message->getIdentifier()) {
                    unset($this->_messages[$type][$id]);
                }
                if (empty($this->_messages[$type])) {
                    unset($this->_messages[$type]);
                }
            }
        }
    }

    /**
     * Retrieve messages collection items
     *
     * @param   string $type
     * @return  array
     */
    public function getItems($type = null)
    {
        if ($type) {
            return $this->_messages[$type] ?? [];
        }

        $arrRes = [];
        foreach ($this->_messages as $messageType => $messages) {
            $arrRes = array_merge($arrRes, $messages);
        }

        return $arrRes;
    }

    /**
     * Retrieve all messages by type
     *
     * @param   string $type
     * @return  array
     */
    public function getItemsByType($type)
    {
        return $this->_messages[$type] ?? [];
    }

    /**
     * Retrieve all error messages
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->getItemsByType(Mage_Core_Model_Message::ERROR);
    }

    /**
     * @return string
     */
    public function toString()
    {
        $out = '';
        $arrItems = $this->getItems();
        foreach ($arrItems as $item) {
            $out .= $item->toString();
        }

        return $out;
    }

    /**
     * Retrieve messages count
     *
     * @param string|null $type
     * @return int
     */
    public function count($type = null)
    {
        if ($type) {
            if (isset($this->_messages[$type])) {
                return count($this->_messages[$type]);
            }
            return 0;
        }
        return count($this->_messages);
    }
}
