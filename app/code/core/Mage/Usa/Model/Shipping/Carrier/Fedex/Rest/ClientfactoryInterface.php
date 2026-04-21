<?php

declare(strict_types=1);

use ShipStream\FedEx\Contracts\TokenCache;

interface Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_ClientfactoryInterface
{
    public function create(
        string $clientId,
        string $clientSecret,
        bool $sandboxMode,
        ?TokenCache $tokenCache = null,
    ): Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_Client;
}
