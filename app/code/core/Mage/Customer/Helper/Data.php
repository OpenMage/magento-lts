<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/**
 * Customer Data Helper
 *
 * @package    Mage_Customer
 */
class Mage_Customer_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Query param name for last url visited
     */
    public const REFERER_QUERY_PARAM_NAME = 'referer';

    /**
     * Route for customer account login page
     */
    public const ROUTE_ACCOUNT_LOGIN = 'customer/account/login';

    /**
     * Config name for Redirect Customer to Account Dashboard after Logging in setting
     */
    public const XML_PATH_CUSTOMER_STARTUP_REDIRECT_TO_DASHBOARD = 'customer/startup/redirect_dashboard';

    /**
     * Config paths to VAT related customer groups
     */
    public const XML_PATH_CUSTOMER_VIV_INTRA_UNION_GROUP = 'customer/create_account/viv_intra_union_group';

    public const XML_PATH_CUSTOMER_VIV_DOMESTIC_GROUP = 'customer/create_account/viv_domestic_group';

    public const XML_PATH_CUSTOMER_VIV_INVALID_GROUP = 'customer/create_account/viv_invalid_group';

    public const XML_PATH_CUSTOMER_VIV_ERROR_GROUP = 'customer/create_account/viv_error_group';

    /**
     * Config path to option that enables/disables automatic group assignment based on VAT
     */
    public const XML_PATH_CUSTOMER_VIV_GROUP_AUTO_ASSIGN = 'customer/create_account/viv_disable_auto_group_assign_default';

    /**
     * Config path to support email
     */
    public const XML_PATH_SUPPORT_EMAIL = 'trans_email/ident_support/email';

    /**
     * WSDL of VAT validation service
     */
    public const VAT_VALIDATION_WSDL_URL = 'https://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl';

    /**
     * Configuration path to expiration period of reset password link
     */
    public const XML_PATH_CUSTOMER_RESET_PASSWORD_LINK_EXPIRATION_PERIOD
        = 'default/customer/password/reset_link_expiration_period';

    /**
     * Configuration path to require admin password on customer password change
     */
    public const XML_PATH_CUSTOMER_REQUIRE_ADMIN_USER_TO_CHANGE_USER_PASSWORD
        = 'customer/password/require_admin_user_to_change_user_password';

    /**
     * Configuration path to password forgotten flow change
     */
    public const XML_PATH_CUSTOMER_FORGOT_PASSWORD_FLOW_SECURE = 'admin/security/forgot_password_flow_secure';

    public const XML_PATH_CUSTOMER_FORGOT_PASSWORD_EMAIL_TIMES = 'admin/security/forgot_password_email_times';

    public const XML_PATH_CUSTOMER_FORGOT_PASSWORD_IP_TIMES    = 'admin/security/forgot_password_ip_times';

    /**
     * VAT class constants
     */
    public const VAT_CLASS_DOMESTIC    = 'domestic';

    public const VAT_CLASS_INTRA_UNION = 'intra_union';

    public const VAT_CLASS_INVALID     = 'invalid';

    public const VAT_CLASS_ERROR       = 'error';

    protected $_moduleName = 'Mage_Customer';

    /**
     * @var Mage_Customer_Model_Customer
     */
    protected $_customer;

    /**
     * Customer groups collection
     *
     * @var Mage_Customer_Model_Resource_Group_Collection
     */
    protected $_groups;

    /**
     * Check customer is logged in
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        return Mage::getSingleton('customer/session')->isLoggedIn();
    }

    /**
     * Retrieve logged in customer
     *
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        if (is_null($this->_customer)) {
            $this->_customer = Mage::getSingleton('customer/session')->getCustomer();
        }

        return $this->_customer;
    }

    /**
     * Retrieve customer groups collection
     *
     * @return Mage_Customer_Model_Resource_Group_Collection
     */
    public function getGroups()
    {
        if (is_null($this->_groups)) {
            $this->_groups = Mage::getModel('customer/group')->getResourceCollection()
                ->setRealGroupsFilter()
                ->load();
        }

        return $this->_groups;
    }

    /**
     * Retrieve current (logged in) customer object
     *
     * @return Mage_Customer_Model_Customer
     */
    public function getCurrentCustomer()
    {
        return $this->getCustomer();
    }

    /**
     * Retrieve full customer name from provided object
     *
     * @param Mage_Newsletter_Model_Subscriber|Mage_Sales_Model_Order|Mage_Sales_Model_Quote $object
     * @return string
     */
    public function getFullCustomerName($object = null)
    {
        $name = '';
        if (is_null($object)) {
            $name = $this->getCustomerName();
        } else {
            $config = Mage::getSingleton('eav/config');

            if ($config->getAttribute('customer', 'prefix')->getIsVisible()
                && ($object->getPrefix() || $object->getCustomerPrefix())
            ) {
                $name .= ($object->getPrefix() ?: $object->getCustomerPrefix()) . ' ';
            }

            $name .= $object->getFirstname() ?: $object->getCustomerFirstname();

            if ($config->getAttribute('customer', 'middlename')->getIsVisible()
                && ($object->getMiddlename() || $object->getCustomerMiddlename())
            ) {
                $name .= ' ' . ($object->getMiddlename() ?: $object->getCustomerMiddlename());
            }

            $name .= ' ' . ($object->getLastname() ?: $object->getCustomerLastname());

            if ($config->getAttribute('customer', 'suffix')->getIsVisible()
                && ($object->getSuffix() || $object->getCustomerSuffix())
            ) {
                $name .= ' ' . ($object->getSuffix() ?: $object->getCustomerSuffix());
            }
        }

        return $name;
    }

    /**
     * Retrieve current customer name
     *
     * @return string
     */
    public function getCustomerName()
    {
        return $this->getCustomer()->getName();
    }

    /**
     * Check customer has address
     *
     * @return bool
     */
    public function customerHasAddresses()
    {
        return count($this->getCustomer()->getAddresses()) > 0;
    }

    /**************************************************************************
     * Customer urls
     */

    /**
     * Retrieve customer login url
     *
     * @return string
     */
    public function getLoginUrl()
    {
        return $this->_getUrl(self::ROUTE_ACCOUNT_LOGIN, $this->getLoginUrlParams());
    }

    /**
     * Retrieve parameters of customer login url
     *
     * @return array
     */
    public function getLoginUrlParams()
    {
        $params = [];

        $referer = $this->_getRequest()->getParam(self::REFERER_QUERY_PARAM_NAME);

        if (!$referer && !Mage::getStoreConfigFlag(self::XML_PATH_CUSTOMER_STARTUP_REDIRECT_TO_DASHBOARD)
            && !Mage::getSingleton('customer/session')->getNoReferer()
        ) {
            $referer = Mage::getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true]);
            $referer = Mage::helper('core')->urlEncode($referer);
        }

        if ($referer) {
            $params = [self::REFERER_QUERY_PARAM_NAME => $referer];
        }

        return $params;
    }

    /**
     * Retrieve customer login POST URL
     *
     * @return string
     */
    public function getLoginPostUrl()
    {
        $params = [];
        if ($this->_getRequest()->getParam(self::REFERER_QUERY_PARAM_NAME)) {
            $params = [
                self::REFERER_QUERY_PARAM_NAME => $this->_getRequest()->getParam(self::REFERER_QUERY_PARAM_NAME),
            ];
        }

        return $this->_getUrl('customer/account/loginPost', $params);
    }

    /**
     * Retrieve customer logout url
     *
     * @return string
     */
    public function getLogoutUrl()
    {
        return $this->_getUrl('customer/account/logout');
    }

    /**
     * Retrieve customer dashboard url
     *
     * @return string
     */
    public function getDashboardUrl()
    {
        return $this->_getUrl('customer/account');
    }

    /**
     * Retrieve customer account page url
     *
     * @return string
     */
    public function getAccountUrl()
    {
        return $this->_getUrl('customer/account');
    }

    /**
     * Retrieve customer register form url
     *
     * @return string
     */
    public function getRegisterUrl()
    {
        return $this->_getUrl('customer/account/create');
    }

    /**
     * Retrieve customer register form post url
     *
     * @return string
     */
    public function getRegisterPostUrl()
    {
        return $this->_getUrl('customer/account/createpost');
    }

    /**
     * Retrieve customer account edit form url
     *
     * @return string
     */
    public function getEditUrl()
    {
        return $this->_getUrl('customer/account/edit');
    }

    /**
     * Retrieve customer edit POST URL
     *
     * @return string
     */
    public function getEditPostUrl()
    {
        return $this->_getUrl('customer/account/editpost');
    }

    /**
     * Retrieve url of forgot password page
     *
     * @return string
     */
    public function getForgotPasswordUrl()
    {
        return $this->_getUrl('customer/account/forgotpassword');
    }

    /**
     * Check is confirmation required
     *
     * @return bool
     */
    public function isConfirmationRequired()
    {
        return $this->getCustomer()->isConfirmationRequired();
    }

    /**
     * Retrieve confirmation URL for Email
     *
     * @param string $email
     * @return string
     */
    public function getEmailConfirmationUrl($email = null)
    {
        return $this->_getUrl('customer/account/confirmation', ['email' => $email]);
    }

    /**
     * Check whether customers registration is allowed
     *
     * @return bool
     */
    public function isRegistrationAllowed()
    {
        $result = new Varien_Object(['is_allowed' => true]);
        Mage::dispatchEvent('customer_registration_is_allowed', ['result' => $result]);
        return $result->getIsAllowed();
    }

    /**
     * Retrieve name prefix dropdown options
     *
     * @param null|int|Mage_Core_Model_Store|string $store
     * @return array|bool
     */
    public function getNamePrefixOptions($store = null)
    {
        return $this->_prepareNamePrefixSuffixOptions(
            Mage::helper('customer/address')->getConfig('prefix_options', $store),
        );
    }

    /**
     * Retrieve name suffix dropdown options
     *
     * @param null|int|Mage_Core_Model_Store|string $store
     * @return array|bool
     */
    public function getNameSuffixOptions($store = null)
    {
        return $this->_prepareNamePrefixSuffixOptions(
            Mage::helper('customer/address')->getConfig('suffix_options', $store),
        );
    }

    /**
     * Unserialize and clear name prefix or suffix options
     *
     * @param string $options
     * @return array|bool
     */
    protected function _prepareNamePrefixSuffixOptions($options)
    {
        $options = trim($options);
        if (empty($options)) {
            return false;
        }

        $result = [];
        $options = explode(';', $options);
        foreach ($options as $value) {
            $value = $this->escapeHtml(trim($value));
            $result[$value] = $value;
        }

        return $result;
    }

    /**
     * Generate unique token for reset password confirmation link
     *
     * @return string
     */
    public function generateResetPasswordLinkToken()
    {
        return Mage::helper('core')->uniqHash();
    }

    /**
     * Generate unique token based on customer Id for reset password confirmation link
     *
     * @param int $customerId
     * @return string
     */
    public function generateResetPasswordLinkCustomerId($customerId)
    {
        return md5(uniqid($customerId . microtime() . mt_rand(), true));
    }

    /**
     * Retrieve customer reset password link expiration period in days
     *
     * @return int
     */
    public function getResetPasswordLinkExpirationPeriod()
    {
        return (int) Mage::getConfig()->getNode(self::XML_PATH_CUSTOMER_RESET_PASSWORD_LINK_EXPIRATION_PERIOD);
    }

    /**
     * Retrieve is require admin password on customer password change
     *
     * @return bool
     */
    public function getIsRequireAdminUserToChangeUserPassword()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_CUSTOMER_REQUIRE_ADMIN_USER_TO_CHANGE_USER_PASSWORD);
    }

    /**
     * Get default customer group id
     *
     * @param int|Mage_Core_Model_Store|string $store
     * @return int
     */
    public function getDefaultCustomerGroupId($store = null)
    {
        return Mage::getStoreConfigAsInt(Mage_Customer_Model_Group::XML_PATH_DEFAULT_ID, $store);
    }

    /**
     * Retrieve forgot password flow secure type
     *
     * @return int
     */
    public function getCustomerForgotPasswordFlowSecure()
    {
        return Mage::getStoreConfigAsInt(self::XML_PATH_CUSTOMER_FORGOT_PASSWORD_FLOW_SECURE);
    }

    /**
     * Retrieve forgot password requests to times per 24 hours from 1 e-mail
     *
     * @return int
     */
    public function getCustomerForgotPasswordEmailTimes()
    {
        return Mage::getStoreConfigAsInt(self::XML_PATH_CUSTOMER_FORGOT_PASSWORD_EMAIL_TIMES);
    }

    /**
     * Retrieve forgot password requests to times per hour from 1 IP
     *
     * @return int
     */
    public function getCustomerForgotPasswordIpTimes()
    {
        return Mage::getStoreConfigAsInt(self::XML_PATH_CUSTOMER_FORGOT_PASSWORD_IP_TIMES);
    }

    /**
     * Retrieve customer group ID based on his VAT number
     *
     * @param string $customerCountryCode
     * @param Varien_Object $vatValidationResult
     * @param int|Mage_Core_Model_Store|string $store
     * @return null|int
     */
    public function getCustomerGroupIdBasedOnVatNumber($customerCountryCode, $vatValidationResult, $store = null)
    {
        $groupId = null;

        $vatClass = $this->getCustomerVatClass($customerCountryCode, $vatValidationResult, $store);

        $vatClassToGroupXmlPathMap = [
            self::VAT_CLASS_DOMESTIC => self::XML_PATH_CUSTOMER_VIV_DOMESTIC_GROUP,
            self::VAT_CLASS_INTRA_UNION => self::XML_PATH_CUSTOMER_VIV_INTRA_UNION_GROUP,
            self::VAT_CLASS_INVALID => self::XML_PATH_CUSTOMER_VIV_INVALID_GROUP,
            self::VAT_CLASS_ERROR => self::XML_PATH_CUSTOMER_VIV_ERROR_GROUP,
        ];

        if (isset($vatClassToGroupXmlPathMap[$vatClass])) {
            $groupId = Mage::getStoreConfigAsInt($vatClassToGroupXmlPathMap[$vatClass], $store);
        }

        return $groupId;
    }

    /**
     * Send request to VAT validation service and return validation result
     *
     * @param string $countryCode
     * @param string $vatNumber
     * @param string $requesterCountryCode
     * @param string $requesterVatNumber
     *
     * @return Varien_Object
     */
    public function checkVatNumber($countryCode, $vatNumber, $requesterCountryCode = '', $requesterVatNumber = '')
    {
        // Default response
        $gatewayResponse = new Varien_Object([
            'is_valid' => false,
            'request_date' => '',
            'request_identifier' => '',
            'request_success' => false,
        ]);

        if (!extension_loaded('soap')) {
            Mage::logException(Mage::exception(
                'Mage_Core',
                Mage::helper('core')->__('PHP SOAP extension is required.'),
            ));
            return $gatewayResponse;
        }

        if (!$this->canCheckVatNumber($countryCode, $vatNumber, $requesterCountryCode, $requesterVatNumber)) {
            return $gatewayResponse;
        }

        $countryCodeForVatNumber = $this->_getCountryCodeForVatNumber($countryCode);
        $requesterCountryCodeForVatNumber = $this->_getCountryCodeForVatNumber($requesterCountryCode);

        try {
            $soapClient = $this->_createVatNumberValidationSoapClient();

            $requestParams = [];
            $requestParams['countryCode'] = $countryCodeForVatNumber;
            $requestParams['vatNumber'] = str_replace([' ', '-'], ['', ''], $vatNumber);
            $requestParams['requesterCountryCode'] = $requesterCountryCodeForVatNumber;
            $requestParams['requesterVatNumber'] = str_replace([' ', '-'], ['', ''], $requesterVatNumber);

            // Send request to service
            $result = $soapClient->checkVatApprox($requestParams);

            $gatewayResponse->setIsValid((bool) $result->valid);
            $gatewayResponse->setRequestDate((string) $result->requestDate);
            $gatewayResponse->setRequestIdentifier((string) $result->requestIdentifier);
            $gatewayResponse->setRequestSuccess(true);
        } catch (Exception) {
            $gatewayResponse->setIsValid(false);
            $gatewayResponse->setRequestDate('');
            $gatewayResponse->setRequestIdentifier('');
        }

        return $gatewayResponse;
    }

    /**
     * Check if parameters are valid to send to VAT validation service
     *
     * @param string $countryCode
     * @param string $vatNumber
     * @param string $requesterCountryCode
     * @param string $requesterVatNumber
     *
     * @return bool
     */
    public function canCheckVatNumber($countryCode, $vatNumber, $requesterCountryCode, $requesterVatNumber)
    {
        $result = true;
        /** @var Mage_Core_Helper_Data $coreHelper */
        $coreHelper = Mage::helper('core');

        if (!is_string($countryCode)
            || !is_string($vatNumber)
            || !is_string($requesterCountryCode)
            || !is_string($requesterVatNumber)
            || empty($countryCode)
            || !$coreHelper->isCountryInEU($countryCode)
            || empty($vatNumber)
            || (empty($requesterCountryCode) && !empty($requesterVatNumber))
            || (!empty($requesterCountryCode) && empty($requesterVatNumber))
            || (!empty($requesterCountryCode) && !$coreHelper->isCountryInEU($requesterCountryCode))
        ) {
            $result = false;
        }

        return $result;
    }

    /**
     * Get VAT class
     *
     * @param string $customerCountryCode
     * @param Varien_Object $vatValidationResult
     * @param null|int|Mage_Core_Model_Store|string $store
     * @return null|string
     */
    public function getCustomerVatClass($customerCountryCode, $vatValidationResult, $store = null)
    {
        $vatClass = null;

        $isVatNumberValid = $vatValidationResult->getIsValid();

        if (is_string($customerCountryCode)
            && !empty($customerCountryCode)
            && $customerCountryCode === Mage::helper('core')->getMerchantCountryCode($store)
            && $isVatNumberValid
        ) {
            $vatClass = self::VAT_CLASS_DOMESTIC;
        } elseif ($isVatNumberValid) {
            $vatClass = self::VAT_CLASS_INTRA_UNION;
        } else {
            $vatClass = self::VAT_CLASS_INVALID;
        }

        if (!$vatValidationResult->getRequestSuccess()) {
            $vatClass = self::VAT_CLASS_ERROR;
        }

        return $vatClass;
    }

    /**
     * Get validation message that will be displayed to user by VAT validation result object
     *
     * @param Mage_Customer_Model_Address $customerAddress
     * @param bool $customerGroupAutoAssignDisabled
     * @param Varien_Object $validationResult
     * @return Varien_Object
     */
    public function getVatValidationUserMessage($customerAddress, $customerGroupAutoAssignDisabled, $validationResult)
    {
        $message = '';
        $isError = true;
        $customerVatClass = $this->getCustomerVatClass($customerAddress->getCountryId(), $validationResult);
        $groupAutoAssignDisabled = Mage::getStoreConfigFlag(self::XML_PATH_CUSTOMER_VIV_GROUP_AUTO_ASSIGN);

        $willChargeTaxMessage    = $this->__('You will be charged tax.');
        $willNotChargeTaxMessage = $this->__('You will not be charged tax.');

        if ($validationResult->getIsValid()) {
            $message = $this->__('Your VAT ID was successfully validated.');
            $isError = false;

            if (!$groupAutoAssignDisabled && !$customerGroupAutoAssignDisabled) {
                $message .= ' ' . ($customerVatClass == self::VAT_CLASS_DOMESTIC
                    ? $willChargeTaxMessage
                    : $willNotChargeTaxMessage);
            }
        } elseif ($validationResult->getRequestSuccess()) {
            $message = sprintf(
                $this->__('The VAT ID entered (%s) is not a valid VAT ID.') . ' ',
                $this->escapeHtml($customerAddress->getVatId()),
            );
            if (!$groupAutoAssignDisabled && !$customerGroupAutoAssignDisabled) {
                $message .= $willChargeTaxMessage;
            }
        } else {
            $contactUsMessage = sprintf(
                $this->__('If you believe this is an error, please contact us at %s'),
                Mage::getStoreConfig(self::XML_PATH_SUPPORT_EMAIL),
            );

            $message = $this->__('Your Tax ID cannot be validated.') . ' '
                . (!$groupAutoAssignDisabled && !$customerGroupAutoAssignDisabled
                    ? $willChargeTaxMessage . ' ' : '')
                . $contactUsMessage;
        }

        $validationMessageEnvelope = new Varien_Object();
        $validationMessageEnvelope->setMessage($message);
        $validationMessageEnvelope->setIsError($isError);

        return $validationMessageEnvelope;
    }

    /**
     * Get customer password creation timestamp or customer account creation timestamp
     *
     * @param int $customerId
     * @return int
     */
    public function getPasswordTimestamp($customerId)
    {
        return Mage::getResourceModel('customer/customer')->getPasswordTimestamp($customerId);
    }

    /**
     * Create SOAP client based on VAT validation service WSDL
     *
     * @param bool $trace
     * @return SoapClient
     */
    protected function _createVatNumberValidationSoapClient($trace = false)
    {
        return new SoapClient(self::VAT_VALIDATION_WSDL_URL, ['trace' => $trace]);
    }

    /**
     * Returns the country code used in VAT number which can be different from the ISO-2 country code.
     */
    protected function _getCountryCodeForVatNumber(string $countryCode): string
    {
        // Greece uses a different code for VAT validation than its ISO-2 country code.
        // See: https://en.wikipedia.org/wiki/VAT_identification_number#European_Union_VAT_identification_numbers

        return $countryCode === 'GR' ? 'EL' : $countryCode;
    }
}
