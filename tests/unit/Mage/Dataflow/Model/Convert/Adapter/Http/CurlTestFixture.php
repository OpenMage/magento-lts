<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Dataflow\Model\Convert\Adapter\Http;

use Mage_Dataflow_Model_Convert_Adapter_Http_Curl;
use Varien_Http_Adapter_Curl;

/**
 * Exposes the URI validation and protocol restriction of the Dataflow curl adapter
 */
final class CurlTestFixture extends Mage_Dataflow_Model_Convert_Adapter_Http_Curl
{
    public function validateUriWrapper(mixed $uri): void
    {
        $this->_validateUri($uri);
    }

    public function restrictProtocolsWrapper(Varien_Http_Adapter_Curl $http): void
    {
        $this->_restrictProtocols($http);
    }
}
