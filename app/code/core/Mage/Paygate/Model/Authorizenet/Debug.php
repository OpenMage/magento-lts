<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paygate
 */

/**
 * @package    Mage_Paygate
 *
 * @method Mage_Paygate_Model_Resource_Authorizenet_Debug _getResource()
 * @method Mage_Paygate_Model_Resource_Authorizenet_Debug getResource()
 * @method string getRequestBody()
 * @method $this setRequestBody(string $value)
 * @method string getResponseBody()
 * @method $this setResponseBody(string $value)
 * @method string getRequestSerialized()
 * @method $this setRequestSerialized(string $value)
 * @method string getResultSerialized()
 * @method $this setResultSerialized(string $value)
 * @method string getRequestDump()
 * @method $this setRequestDump(string $value)
 * @method string getResultDump()
 * @method $this setResultDump(string $value)
 */
class Mage_Paygate_Model_Authorizenet_Debug extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('paygate/authorizenet_debug');
    }
}
