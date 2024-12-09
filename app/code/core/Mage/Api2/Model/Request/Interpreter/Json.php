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
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Request content interpreter JSON adapter
 *
 * @category   Mage
 * @package    Mage_Api2
 */
class Mage_Api2_Model_Request_Interpreter_Json implements Mage_Api2_Model_Request_Interpreter_Interface
{
    /**
     * Parse Request body into array of params
     *
     * @param string $body  Posted content from request
     * @return array|null   Return NULL if content is invalid
     * @throws Exception|Mage_Api2_Exception
     */
    public function interpret($body)
    {
        if (!is_string($body)) {
            throw new Exception(sprintf('Invalid data type "%s". String expected.', gettype($body)));
        }

        try {
            $decoded = Zend_Json::decode($body);
        } catch (Zend_Json_Exception $e) {
            throw new Mage_Api2_Exception('Decoding error.', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }

        if ($body != 'null' && $decoded === null) {
            throw new Mage_Api2_Exception('Decoding error.', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }

        return $decoded;
    }
}
