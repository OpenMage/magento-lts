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
 * @method Mage_Paygate_Model_Resource_Authorizenet_Debug_Collection getCollection()
 * @method string getRequestBody()
 * @method string getRequestDump()
 * @method string getRequestSerialized()
 * @method Mage_Paygate_Model_Resource_Authorizenet_Debug getResource()
 * @method Mage_Paygate_Model_Resource_Authorizenet_Debug_Collection getResourceCollection()
 * @method string getResponseBody()
 * @method string getResultDump()
 * @method string getResultSerialized()
 * @method $this setRequestBody(string $value)
 * @method $this setRequestDump(string $value)
 * @method $this setRequestSerialized(string $value)
 * @method $this setResponseBody(string $value)
 * @method $this setResultDump(string $value)
 * @method $this setResultSerialized(string $value)
 */
class Mage_Paygate_Model_Authorizenet_Debug extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('paygate/authorizenet_debug');
    }
}
