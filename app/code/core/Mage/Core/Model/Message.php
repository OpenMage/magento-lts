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
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Message model
 *
 * @category   Mage
 * @package    Mage_Core
 */
class Mage_Core_Model_Message
{
    public const ERROR     = 'error';
    public const WARNING   = 'warning';
    public const NOTICE    = 'notice';
    public const SUCCESS   = 'success';

    /**
     * @param string $code
     * @param string $type
     * @param string $class
     * @param string $method
     * @return Mage_Core_Model_Message_Error|Mage_Core_Model_Message_Notice|Mage_Core_Model_Message_Success|Mage_Core_Model_Message_Warning
     */
    protected function _factory($code, $type, $class = '', $method = '')
    {
        switch (strtolower($type)) {
            case self::ERROR:
                $message = new Mage_Core_Model_Message_Error($code);
                break;
            case self::WARNING:
                $message = new Mage_Core_Model_Message_Warning($code);
                break;
            case self::SUCCESS:
                $message = new Mage_Core_Model_Message_Success($code);
                break;
            default:
                $message = new Mage_Core_Model_Message_Notice($code);
                break;
        }
        $message->setClass($class);
        $message->setMethod($method);

        return $message;
    }

    /**
     * @param string $code
     * @param string $class
     * @param string $method
     * @return Mage_Core_Model_Message_Error|Mage_Core_Model_Message_Notice|Mage_Core_Model_Message_Success|Mage_Core_Model_Message_Warning
     */
    public function error($code, $class = '', $method = '')
    {
        return $this->_factory($code, self::ERROR, $class, $method);
    }

    /**
     * @param string $code
     * @param string $class
     * @param string $method
     * @return Mage_Core_Model_Message_Error|Mage_Core_Model_Message_Notice|Mage_Core_Model_Message_Success|Mage_Core_Model_Message_Warning
     */
    public function warning($code, $class = '', $method = '')
    {
        return $this->_factory($code, self::WARNING, $class, $method);
    }

    /**
     * @param string $code
     * @param string $class
     * @param string $method
     * @return Mage_Core_Model_Message_Error|Mage_Core_Model_Message_Notice|Mage_Core_Model_Message_Success|Mage_Core_Model_Message_Warning
     */
    public function notice($code, $class = '', $method = '')
    {
        return $this->_factory($code, self::NOTICE, $class, $method);
    }

    /**
     * @param string $code
     * @param string $class
     * @param string $method
     * @return Mage_Core_Model_Message_Error|Mage_Core_Model_Message_Notice|Mage_Core_Model_Message_Success|Mage_Core_Model_Message_Warning
     */
    public function success($code, $class = '', $method = '')
    {
        return $this->_factory($code, self::SUCCESS, $class, $method);
    }
}
