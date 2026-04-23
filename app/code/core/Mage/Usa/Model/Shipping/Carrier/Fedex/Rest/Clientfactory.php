<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Usa
 */

use ShipStream\FedEx\Contracts\TokenCache;
use ShipStream\FedEx\Enums\Endpoint;
use ShipStream\FedEx\FedEx;

class Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_Clientfactory implements Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_ClientfactoryInterface
{
    public function create(
        string $clientId,
        string $clientSecret,
        bool $sandboxMode,
        ?TokenCache $tokenCache = null,
    ): Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_Client {
        $tokenCache ??= Mage::getSingleton('usa/shipping_carrier_fedex_rest_tokenmanager');

        $connector = new FedEx(
            clientId: $clientId,
            clientSecret: $clientSecret,
            endpoint: $sandboxMode ? Endpoint::SANDBOX : Endpoint::PROD,
            tokenCache: $tokenCache,
        );

        return Mage::getModel('usa/shipping_carrier_fedex_rest_client', $connector);
    }
}
