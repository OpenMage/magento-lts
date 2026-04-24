<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

namespace OpenMage\Tests\Functional\Usa\Shipping\Carrier;

use Override;
use Dotenv\Dotenv;
use Mage;
use Mage_Usa_Model_Shipping_Carrier_Fedex;
use OpenMage\Tests\Functional\FunctionalTest;
use OpenMage\Tests\Functional\Traits\DataProvider\Usa\Shipping\Carrier\FedexTrait;

abstract class FedexTestCase extends FunctionalTest
{
    use FedexTrait;

    /**
     * @var list<string>
     */
    private const ENCRYPTED_BACKEND_FIELDS = [
        'account',
        'client_id',
        'client_secret',
        'tracking_client_id',
        'tracking_client_secret',
    ];

    private static bool $envLoaded = false;

    private static bool $fedexConfigApplied = false;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        if (!self::$envLoaded) {
            $envDir = __DIR__ . '/../../../../functional';
            if (is_file($envDir . '/.env.fedex')) {
                Dotenv::createImmutable($envDir, '.env.fedex')->safeLoad();
            }

            self::$envLoaded = true;
        }

        parent::setUpBeforeClass();
    }

    protected function setUp(): void
    {
        parent::setUp();

        if (!self::$fedexConfigApplied) {
            $this->applyFedexConfig();
            self::$fedexConfigApplied = true;
        }
    }

    protected function buildFedexCarrier(): Mage_Usa_Model_Shipping_Carrier_Fedex
    {
        $carrier = Mage::getModel('usa/shipping_carrier_fedex');
        $carrier->setStore(Mage::app()->getStore());
        $carrier->setData('cache_enabled', false);

        return $carrier;
    }

    private function applyFedexConfig(): void
    {
        $store = Mage::app()->getStore();

        $values = [
            'active' => '1',
            'title' => 'FedEx',
            'client_id' => self::env('FEDEX_CLIENT_ID', ''),
            'client_secret' => self::env('FEDEX_CLIENT_SECRET', ''),
            'tracking_client_id' => self::env('FEDEX_TRACKING_CLIENT_ID', ''),
            'tracking_client_secret' => self::env('FEDEX_TRACKING_CLIENT_SECRET', ''),
            'account' => self::env('FEDEX_ACCOUNT', ''),
            'sandbox_mode' => self::env('FEDEX_SANDBOX_MODE', '1'),
            'allowed_methods' => $this->allowedMethods,
            'packaging' => 'YOUR_PACKAGING',
            'dropoff' => 'REGULAR_PICKUP',
            'unit_of_measure' => 'LB',
            'residence_delivery' => '0',
            'smartpost_hubid' => '',
            'specificerrmsg' => 'FedEx is unavailable.',
            'debug' => '1',
        ];

        $encryptor = Mage::helper('core');
        foreach ($values as $key => $value) {
            $stringValue = (string) $value;
            if ($stringValue !== '' && in_array($key, self::ENCRYPTED_BACKEND_FIELDS, true)) {
                $stringValue = $encryptor->encrypt($stringValue);
            }

            $store->setConfig('carriers/fedex/' . $key, $stringValue);
        }
    }
}
