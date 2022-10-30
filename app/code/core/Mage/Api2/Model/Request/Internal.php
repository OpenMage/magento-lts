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
 * @package    Mage_Api2
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * API internal request model
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
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
