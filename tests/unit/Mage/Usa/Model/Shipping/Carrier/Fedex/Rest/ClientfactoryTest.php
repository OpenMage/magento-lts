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
use Saloon\Http\Auth\AccessTokenAuthenticator;
use ShipStream\FedEx\Contracts\TokenCache;
use ShipStream\FedEx\Enums\Endpoint;
use ShipStream\FedEx\FedEx;

final class ClientfactoryTest extends OpenMageTest
{
    public function testCreateReturnsClientInstance(): void
    {
        $client = (new ClientFactory())->create('id', 'secret', true);

        self::assertInstanceOf(Client::class, $client);
    }

    public function testCreateUsesTokenManagerSingletonByDefault(): void
    {
        $expected = Mage::getSingleton('usa/shipping_carrier_fedex_rest_tokenmanager');

        $client = (new ClientFactory())->create('id', 'secret', true);

        self::assertInstanceOf(TokenManager::class, $expected);
        self::assertSame($expected, $this->connector($client)->tokenCache);
    }

    public function testCreateUsesProvidedTokenCacheWhenSupplied(): void
    {
        $custom = new class implements TokenCache {
            public static function get(string $key): AccessTokenAuthenticator|false
            {
                return false;
            }

            public static function set(string $key, AccessTokenAuthenticator $authenticator): void
            {
            }
        };

        $client = (new ClientFactory())->create('id', 'secret', true, $custom);

        self::assertSame($custom, $this->connector($client)->tokenCache);
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
        $property = new ReflectionProperty(Client::class, 'connector');
        return $property->getValue($client);
    }
}
