<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api2
 */

/**
 * API Response model
 *
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
        return (bool) count($this->_messages) > 0;
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
