<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Usa
 */

use Monolog\Level;

/**
 * Abstract base class for USPS REST API service classes.
 *
 * Provides shared REST client initialization, OAuth authentication,
 * and debug logging used by Address, Standards, and Label services.
 *
 * @package    Mage_Usa
 */
abstract class Mage_Usa_Model_Shipping_Carrier_Usps_AbstractService
{
    protected ?Mage_Usa_Model_Shipping_Carrier_Usps_Rest_Client $_client = null;

    protected bool $_debug = false;

    /**
     * Prefix for debug log messages — override in subclasses
     */
    protected string $_debugPrefix = 'USPS Service';

    public function __construct(?Mage_Usa_Model_Shipping_Carrier_Usps_Rest_Client $client = null)
    {
        $this->_client = $client;
        $this->_debug = Mage::getStoreConfigFlag('carriers/usps/debug');
    }

    /**
     * Get REST client instance, initializing with OAuth token on first call
     */
    protected function _getClient(): Mage_Usa_Model_Shipping_Carrier_Usps_Rest_Client
    {
        if (!$this->_client instanceof \Mage_Usa_Model_Shipping_Carrier_Usps_Rest_Client) {
            $this->_client = Mage::getModel('usa/shipping_carrier_usps_rest_client');

            $baseUrl = Mage::getStoreConfig('carriers/usps/gateway_url');
            if ($baseUrl) {
                $this->_client->setBaseUrl($baseUrl);
            }

            $auth = Mage::getModel('usa/shipping_carrier_uspsAuth');
            $clientId = Mage::helper('core')->decrypt(Mage::getStoreConfig('carriers/usps/client_id'));
            $clientSecret = Mage::helper('core')->decrypt(Mage::getStoreConfig('carriers/usps/client_secret'));
            $gatewayUrl = Mage::getStoreConfig('carriers/usps/gateway_url');

            $token = $auth->getAccessToken($clientId, $clientSecret, $gatewayUrl);
            if ($token) {
                $this->_client->setAccessToken($token);
            }
        }

        return $this->_client;
    }

    /**
     * Debug logging
     *
     * @param array<string, mixed> $data
     */
    protected function _debug(array $data): void
    {
        if (!$this->_debug) {
            return;
        }

        Mage::log(
            $this->_debugPrefix . ': ' . json_encode($data),
            Level::Debug,
            'shipping_usps.log',
            true,
        );
    }
}
