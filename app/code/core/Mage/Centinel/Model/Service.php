<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Centinel
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * 3D Secure Validation Model
 *
 * @category   Mage
 * @package    Mage_Centinel
 */
class Mage_Centinel_Model_Service extends Varien_Object
{
    /**
     * Cmpi public keys
     */
    public const CMPI_PARES    = 'centinel_authstatus';
    public const CMPI_ENROLLED = 'centinel_mpivendor';
    public const CMPI_CAVV     = 'centinel_cavv';
    public const CMPI_ECI      = 'centinel_eci';
    public const CMPI_XID      = 'centinel_xid';

    /**
     * State cmpi results to public map
     *
     * @var array
     */
    protected $_cmpiMap = [
        'lookup_enrolled'      => self::CMPI_ENROLLED,
        'lookup_eci_flag'      => self::CMPI_ECI,
        'authenticate_pa_res_status' => self::CMPI_PARES,
        'authenticate_cavv'          => self::CMPI_CAVV,
        'authenticate_eci_flag'      => self::CMPI_ECI,
        'authenticate_xid'           => self::CMPI_XID,
    ];

    /**
     * Validation api model
     *
     * @var Mage_Centinel_Model_Api|null
     */
    protected $_api;

    /**
     * Validation state model
     *
     * @var Mage_Centinel_Model_StateAbstract|false
     */
    protected $_validationState;

    /**
     * Return validation session object
     *
     * @return Mage_Centinel_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('centinel/session');
    }

    /**
     * Return value from section of centinel config
     *
     * @return Mage_Centinel_Model_Config
     */
    protected function _getConfig()
    {
        $config = Mage::getSingleton('centinel/config');
        return $config->setStore($this->getStore());
    }

    /**
     * Generate checksum from all passed parameters
     *
     * @param string $cardType
     * @param string $cardNumber
     * @param string $cardExpMonth
     * @param string $cardExpYear
     * @param double $amount
     * @param string $currencyCode
     * @return string
     */
    protected function _generateChecksum($paymentMethodCode, $cardType, $cardNumber, $cardExpMonth, $cardExpYear, $amount, $currencyCode)
    {
        return md5(implode('_', func_get_args()));
    }

    /**
     * Unified validation/authentication URL getter
     *
     * @param string $suffix
     * @param bool $current
     * @return string
     */
    private function _getUrl($suffix, $current = false)
    {
        $params = [
            '_secure'  => true,
            '_current' => $current,
            'form_key' => Mage::getSingleton('core/session')->getFormKey(),
            'isIframe' => true,
        ];
        if (Mage::app()->getStore()->isAdmin()) {
            return Mage::getSingleton('adminhtml/url')->getUrl('*/centinel_index/' . $suffix, $params);
        } else {
            return Mage::getUrl('centinel/index/' . $suffix, $params);
        }
    }

    /**
     * Return validation api model
     *
     * @return Mage_Centinel_Model_Api
     */
    protected function _getApi()
    {
        if ($this->_api !== null) {
            return $this->_api;
        }

        $this->_api = Mage::getSingleton('centinel/api');
        $config = $this->_getConfig();
        $this->_api
           ->setProcessorId($config->getProcessorId())
           ->setMerchantId($config->getMerchantId())
           ->setTransactionPwd($config->getTransactionPwd())
           ->setIsTestMode($config->getIsTestMode())
           ->setDebugFlag($config->getDebugFlag())
           ->setApiEndpointUrl($this->getCustomApiEndpointUrl());
        return $this->_api;
    }

    /**
     * Create and return validation state model for card type
     *
     * @param string $cardType
     * @return Mage_Centinel_Model_StateAbstract|false
     */
    protected function _getValidationStateModel($cardType)
    {
        if ($modelClass = $this->_getConfig()->getStateModelClass($cardType)) {
            /** @var Mage_Centinel_Model_StateAbstract $model */
            $model = Mage::getModel($modelClass);
            return $model;
        }
        return false;
    }

    /**
     * Return validation state model
     *
     * @param string $cardType
     * @return Mage_Centinel_Model_StateAbstract|false
     */
    protected function _getValidationState($cardType = null)
    {
        $type = $cardType ? $cardType : $this->_getSession()->getData('card_type');
        if (!$this->_validationState && $type) {
            $model = $this->_getValidationStateModel($type);
            if (!$model) {
                return false;
            }
            $model->setDataStorage($this->_getSession());
            $this->_validationState = $model;
        }
        return $this->_validationState;
    }

    /**
     * Drop validation state model
     *
     */
    protected function _resetValidationState()
    {
        $this->_getSession()->setData([]);
        $this->_validationState = false;
    }

    /**
     * Drop old and init new validation state model
     *
     * @param string $cardType
     * @param string $dataChecksum
     * @return Mage_Centinel_Model_StateAbstract
     */
    protected function _initValidationState($cardType, $dataChecksum)
    {
        $this->_resetValidationState();
        $state = $this->_getValidationStateModel($cardType);
        $state->setDataStorage($this->_getSession())
            ->setCardType($cardType)
            ->setChecksum($dataChecksum)
            ->setIsModeStrict($this->getIsModeStrict());
        return $this->_getValidationState();
    }

    /**
     * Process lookup validation and init new validation state model
     *
     * @param Varien_Object $data
     */
    public function lookup($data)
    {
        $newChecksum = $this->_generateChecksum(
            $data->getPaymentMethodCode(),
            $data->getCardType(),
            $data->getCardNumber(),
            $data->getCardExpMonth(),
            $data->getCardExpYear(),
            $data->getAmount(),
            $data->getCurrencyCode(),
        );

        $validationState = $this->_initValidationState($data->getCardType(), $newChecksum);

        $api = $this->_getApi();
        $result = $api->callLookup($data);
        $validationState->setLookupResult($result);
    }

    /**
     * Process authenticate validation
     *
     * @param Varien_Object $data
     */
    public function authenticate($data)
    {
        $validationState = $this->_getValidationState();
        if (!$validationState || $data->getTransactionId() != $validationState->getLookupTransactionId()) {
            throw new Exception('Authentication impossible: transaction id or validation state is wrong.');
        }

        $api = $this->_getApi();
        $result = $api->callAuthentication($data);
        $validationState->setAuthenticateResult($result);
        if (!$validationState->isAuthenticateSuccessful()) {
            $this->reset();
        }
    }

    /**
     * Validate payment data
     *
     * This check is performed on payment information submission, as well as on placing order.
     * Workflow state is stored validation state model
     *
     * @param Varien_Object $data
     * @throws Mage_Core_Exception
     */
    public function validate($data)
    {
        $newChecksum = $this->_generateChecksum(
            $data->getPaymentMethodCode(),
            $data->getCardType(),
            $data->getCardNumber(),
            $data->getCardExpMonth(),
            $data->getCardExpYear(),
            $data->getAmount(),
            $data->getCurrencyCode(),
        );

        $validationState = $this->_getValidationState($data->getCardType());
        if (!$validationState) {
            $this->_resetValidationState();
            return;
        }

        // check whether is authenticated before placing order
        if ($this->getIsPlaceOrder()) {
            if ($validationState->getChecksum() != $newChecksum) {
                Mage::throwException(Mage::helper('centinel')->__('Payment information error. Please start over.'));
            }
            if ($validationState->isAuthenticateSuccessful()) {
                return;
            }
            Mage::throwException(Mage::helper('centinel')->__('Please verify the card with the issuer bank before placing the order.'));
        } else {
            if ($validationState->getChecksum() != $newChecksum || !$validationState->isLookupSuccessful()) {
                $this->lookup($data);
                $validationState = $this->_getValidationState();
            }
            if ($validationState->isLookupSuccessful()) {
                return;
            }
            Mage::throwException(Mage::helper('centinel')->__('This card has failed validation and cannot be used.'));
        }
    }

    /**
     * Reset validation state and drop api object
     *
     * @return $this
     */
    public function reset()
    {
        $this->_resetValidationState();
        $this->_api = null;
        return $this;
    }

    /**
     * Return URL for authentication
     *
     * @return string
     */
    public function getAuthenticationStartUrl()
    {
        return $this->_getUrl('authenticationstart');
    }

    /**
     * Return URL for validation
     *
     * @return string
     */
    public function getValidatePaymentDataUrl()
    {
        return $this->_getUrl('validatepaymentdata');
    }

    /**
     * If authenticate is should return true
     *
     * @return bool
     */
    public function shouldAuthenticate()
    {
        $validationState = $this->_getValidationState();
        return $validationState && $validationState->isAuthenticateAllowed();
    }

    /**
     * Return data for start authentication (redirect customer to bank page)
     *
     * @return array
     */
    public function getAuthenticateStartData()
    {
        $validationState = $this->_getValidationState();
        if (!$validationState && $this->shouldAuthenticate()) {
            throw new Exception('Authentication impossible: validation state is wrong.');
        }
        return [
            'acs_url' => $validationState->getLookupAcsUrl(),
            'pa_req' => $validationState->getLookupPayload(),
            'term_url' => $this->_getUrl('authenticationcomplete', true),
            'md' => $validationState->getLookupTransactionId(),
        ];
    }

    /**
     * If authenticate is successful return true
     *
     * @return bool
     */
    public function isAuthenticateSuccessful()
    {
        $validationState = $this->_getValidationState();
        return $validationState && $validationState->isAuthenticateSuccessful();
    }

    /**
    * Export cmpi lookups and authentication information stored in session into array
    *
    * @param mixed $to
    * @param array|false $map
    * @return mixed $to
    */
    public function exportCmpiData($to, $map = false)
    {
        if (!$map) {
            $map = $this->_cmpiMap;
        }
        if ($validationState = $this->_getValidationState()) {
            $to = Varien_Object_Mapper::accumulateByMap($validationState, $to, $map);
        }
        return $to;
    }
}
