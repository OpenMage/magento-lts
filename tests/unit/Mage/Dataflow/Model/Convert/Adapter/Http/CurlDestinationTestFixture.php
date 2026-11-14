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
use Override;
use Varien_Http_Adapter_Curl;

/**
 * Exposes the destination check of the Dataflow curl adapter and resolves host names from $dns
 * instead of DNS
 */
final class CurlDestinationTestFixture extends Mage_Dataflow_Model_Convert_Adapter_Http_Curl
{
    /**
     * @var array<string, list<string>>
     */
    public array $dns = [];

    public function restrictDestinationWrapper(Varien_Http_Adapter_Curl $http, string $uri): string
    {
        return $this->_restrictDestination($http, $uri);
    }

    /**
     * @return list<string>
     */
    #[Override]
    protected function _resolveHost(string $host): array
    {
        $host = trim($host, '[]');
        if (filter_var($host, FILTER_VALIDATE_IP) !== false) {
            return [$host];
        }

        return $this->dns[$host] ?? [];
    }
}
