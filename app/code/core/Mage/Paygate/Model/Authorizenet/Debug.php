<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paygate
 */

/**
 * @package    Mage_Paygate
 *
 * @method Mage_Paygate_Model_Resource_Authorizenet_Debug            _getResource()
 * @method Mage_Paygate_Model_Resource_Authorizenet_Debug_Collection getCollection()
 * @method Mage_Paygate_Model_Resource_Authorizenet_Debug            getResource()
 * @method Mage_Paygate_Model_Resource_Authorizenet_Debug_Collection getResourceCollection()
 */
class Mage_Paygate_Model_Authorizenet_Debug extends Mage_Core_Model_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('paygate/authorizenet_debug');
    }

    public function getRequestBody(): string
    {
        return (string) $this->_getData('request_body');
    }

    public function getRequestDump(): string
    {
        return (string) $this->_getData('request_dump');
    }

    public function getRequestSerialized(): string
    {
        return (string) $this->_getData('request_serialized');
    }

    public function getResponseBody(): string
    {
        return (string) $this->_getData('response_body');
    }

    public function getResultDump(): string
    {
        return (string) $this->_getData('result_dump');
    }

    public function getResultSerialized(): string
    {
        return (string) $this->_getData('result_serialized');
    }

    public function setRequestBody(string $value): static
    {
        return $this->setData('request_body', $value);
    }

    public function setRequestDump(string $value): static
    {
        return $this->setData('request_dump', $value);
    }

    public function setRequestSerialized(string $value): static
    {
        return $this->setData('request_serialized', $value);
    }

    public function setResponseBody(string $value): static
    {
        return $this->setData('response_body', $value);
    }

    public function setResultDump(string $value): static
    {
        return $this->setData('result_dump', $value);
    }

    public function setResultSerialized(string $value): static
    {
        return $this->setData('result_serialized', $value);
    }
}
