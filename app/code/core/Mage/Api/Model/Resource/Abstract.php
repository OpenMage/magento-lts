<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api
 */

/**
 * Api resource abstract
 *
 * @package    Mage_Api
 */
class Mage_Api_Model_Resource_Abstract
{
    /**
     * Resource configuration
     *
     * @var Varien_Simplexml_Element
     */
    protected $_resourceConfig = null;

    /**
     * Fields API reads never return, whatever the entity type
     *
     * Card numbers and security codes (plain or encrypted with the store key), raw gateway debug payloads,
     * which can contain them, and the encrypted password a quote keeps for a customer who registers during
     * checkout. Card metadata such as cc_type, cc_last4, cc_owner and cc_exp_* and gateway references such
     * as cc_trans_id and last_trans_id are still returned.
     *
     * This list applies to reads only. $_ignoredAttributeCodes also governs writes, and subclasses replace
     * its entries, so it is not used for secrets.
     *
     * @var string[]
     */
    protected $_secretFields = [
        'cc_number',
        'cc_number_enc',
        'cc_cid',
        'cc_cid_enc',
        'cc_debug_request_body',
        'cc_debug_response_body',
        'cc_debug_response_serialized',
        'password_hash',
    ];

    /**
     * Keys of a payment's additional_information that API reads never return
     *
     * PayPal Payflow Link keeps the secret that authenticates its silent post callback and the hosted
     * form's secure token here.
     *
     * @var string[]
     */
    protected $_paymentSecretInformationKeys = ['secure_silent_post_hash', 'secure_token', 'secure_token_id'];

    /**
     * Remove stored secrets from entity data returned by the API
     *
     * @param  array<string, mixed> $data
     * @return array<string, mixed>
     */
    protected function _removeSecrets(array $data)
    {
        foreach ($this->_secretFields as $field) {
            unset($data[$field]);
        }

        if (isset($data['additional_information']) && is_array($data['additional_information'])) {
            foreach ($this->_paymentSecretInformationKeys as $key) {
                unset($data['additional_information'][$key]);
            }
        }

        return $data;
    }

    /**
     * Retrieve webservice session
     *
     * @return Mage_Api_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('api/session');
    }

    /**
     * Retrieve webservice configuration
     *
     * @return Mage_Api_Model_Config
     */
    protected function _getConfig()
    {
        return Mage::getSingleton('api/config');
    }

    /**
     * Set configuration for api resource
     *
     * @return $this
     */
    public function setResourceConfig(Varien_Simplexml_Element $xml)
    {
        $this->_resourceConfig = $xml;
        return $this;
    }

    /**
     * Retrieve configuration for api resource
     *
     * @return Varien_Simplexml_Element
     */
    public function getResourceConfig()
    {
        return $this->_resourceConfig;
    }

    /**
     * Retrieve webservice server
     *
     * @return Mage_Api_Model_Server
     */
    protected function _getServer()
    {
        return Mage::getSingleton('api/server');
    }

    /**
     * Dispatches fault
     *
     * @param  string             $code
     * @param  null|string        $customMessage
     * @return never
     * @throws Mage_Api_Exception
     */
    protected function _fault($code, $customMessage = null)
    {
        throw new Mage_Api_Exception($code, $customMessage);
    }
}
