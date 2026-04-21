<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

namespace OpenMage\Tests\Unit\Mage\Usa\Model\Shipping\Carrier\Fedex\Rest;

use Mage;
use Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_Client as Client;
use Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_Clientfactory as ClientFactory;
use Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_Tokenmanager as TokenManager;
use OpenMage\Tests\Unit\OpenMageTest;
use ReflectionProperty;
use ShipStream\FedEx\Enums\Endpoint;
use ShipStream\FedEx\FedEx;

final class ClientfactoryTest extends OpenMageTest
{
    public function testCreateUsesTokenManagerSingletonByDefault(): void
    {
        $expected = Mage::getSingleton('usa/shipping_carrier_fedex_rest_tokenmanager');

        $client = (new ClientFactory())->create('id', 'secret', true);

        self::assertInstanceOf(TokenManager::class, $expected);
        self::assertSame($expected, $this->connector($client)->tokenCache);
    }

    public function testCreateUsesProvidedTokenCacheWhenSupplied(): void
    {
        $defaultCache = Mage::getSingleton('usa/shipping_carrier_fedex_rest_tokenmanager');
        $customCache = Mage::getModel('usa/shipping_carrier_fedex_rest_tokenmanager');

        $client = (new ClientFactory())->create('id', 'secret', true, $customCache);
        $connector = $this->connector($client);

        self::assertNotSame($defaultCache, $connector->tokenCache);
        self::assertSame($customCache, $connector->tokenCache);
    }

    public function testCreateMapsSandboxFlagToEndpoint(): void
    {
        $sandboxClient = (new ClientFactory())->create('id', 'secret', true);
        $prodClient    = (new ClientFactory())->create('id', 'secret', false);

        self::assertSame(Endpoint::SANDBOX, $this->connector($sandboxClient)->endpoint);
        self::assertSame(Endpoint::PROD, $this->connector($prodClient)->endpoint);
    }

    private function connector(Client $client): FedEx
    {
        return (new ReflectionProperty(Client::class, 'connector'))->getValue($client);
    }
}
