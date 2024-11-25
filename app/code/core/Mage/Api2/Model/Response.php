<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Api2
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * API Response model
 *
 * @category   Mage
 * @package    Mage_Api2
 */
class Mage_Api2_Model_Response extends Zend_Controller_Response_Http
{
    /**
     * Character set which must be used in response
     */
    public const RESPONSE_CHARSET = 'utf-8';

    /**
     * Default message types
     */
    public const MESSAGE_TYPE_SUCCESS = 'success';
    public const MESSAGE_TYPE_ERROR   = 'error';
    public const MESSAGE_TYPE_WARNING = 'warning';

    /**
     * Messages
     *
     * @var array
     */
    protected $_messages = [];

    /**
     * Set header appropriate to specified MIME type
     *
     * @param string $mimeType MIME type
     * @return $this
     */
    public function setMimeType($mimeType)
    {
        return $this->setHeader('Content-Type', "{$mimeType}; charset=" . self::RESPONSE_CHARSET, true);
    }

    /**
     * Add message to response
     *
     * @param string $message
     * @param string $code
     * @param array $params
     * @param string $type
     * return Mage_Api2_Model_Response
     * @return Mage_Api2_Model_Response
     */
    public function addMessage($message, $code, $params = [], $type = self::MESSAGE_TYPE_ERROR)
    {
        $params['message'] = $message;
        $params['code'] = $code;
        $this->_messages[$type][] = $params;
        return $this;
    }

    /**
     * Has messages
     *
     * @return bool
     */
    public function hasMessages()
    {
        return (bool)count($this->_messages) > 0;
    }

    /**
     * Return messages
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->_messages;
    }

    /**
     * Clear messages
     *
     * return Mage_Api2_Model_Response
     */
    public function clearMessages()
    {
        $this->_messages = [];
        return $this;
    }
}
