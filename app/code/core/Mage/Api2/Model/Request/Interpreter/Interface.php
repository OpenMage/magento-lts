<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api2
 */

/**
 * Request content interpreter adapter interface
 *
 * @package    Mage_Api2
 */
interface Mage_Api2_Model_Request_Interpreter_Interface
{
    /**
     * Parse request body into array of params
     *
     * @param string $body  Posted content from request
     * @return null|array   Return NULL if content is invalid
     */
    public function interpret($body);
}
