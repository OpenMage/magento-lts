<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api2
 */

/**
 * API internal request model
 *
 * @package    Mage_Api2
 */
class Mage_Api2_Model_Request_Internal extends Mage_Api2_Model_Request
{
    /**
     * Request body data
     *
     * @var array
     */
    protected $_bodyParams;

    /**
     * Request method
     *
     * @var string
     */
    protected $_method;

    /**
     * Fetch data from HTTP Request body
     *
     * @return array
     */
    public function getBodyParams()
    {
        if ($this->_bodyParams === null) {
            $this->_bodyParams = $this->_getInterpreter()->interpret((string) $this->getRawBody());
        }

        return $this->_bodyParams;
    }

    /**
     * Set request body data
     *
     * @param array $data
     * @return Mage_Api2_Model_Request
     */
    public function setBodyParams($data)
    {
        $this->_bodyParams = $data;
        return $this;
    }

    /**
     * Set HTTP request method for request emulation during internal call
     *
     * @param string $method
     * @return $this
     */
    public function setMethod($method)
    {
        $availableMethod = ['GET', 'POST', 'PUT', 'DELETE'];
        if (in_array($method, $availableMethod)) {
            $this->_method = $method;
        } else {
            throw new Mage_Api2_Exception('Invalid method provided', Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        }

        return $this;
    }

    /**
     * Override parent method for request emulation during internal call
     *
     * @return string
     */
    public function getMethod()
    {
        $method = $this->_method;
        if (!$method) {
            $method = parent::getMethod();
        }

        return $method;
    }
}
