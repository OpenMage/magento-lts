<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Magento Core Exception
 *
 * This class will be extended by other modules
 *
 * @category   Mage
 * @package    Mage_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Exception extends Exception
{
    protected $_messages = [];

    /**
     * @param Mage_Core_Model_Message_Abstract $message
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
