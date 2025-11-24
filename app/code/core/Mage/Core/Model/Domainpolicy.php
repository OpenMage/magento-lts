<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * @package    Mage_Core
 */
class Mage_Core_Model_Domainpolicy
{
    /**
     * X-Frame-Options allow (header is absent)
     */
    public const FRAME_POLICY_ALLOW = 1;

    /**
     * X-Frame-Options SAMEORIGIN
     */
    public const FRAME_POLICY_ORIGIN = 2;

    /**
     * Path to backend domain policy settings
     */
    public const XML_DOMAIN_POLICY_BACKEND = 'admin/security/domain_policy_backend';

    /**
     * Path to frontend domain policy settings
     */
    public const XML_DOMAIN_POLICY_FRONTEND = 'admin/security/domain_policy_frontend';

    /**
     * Current store
     *
     * @var Mage_Core_Model_Store
     */
    protected $_store;

    /**
     * Mage_Core_Model_Domainpolicy constructor.
     * @param array $options
     * @throws Mage_Core_Model_Store_Exception
     */
    public function __construct($options = [])
    {
        $this->_store = $options['store'] ?? Mage::app()->getStore();
    }

    /**
     * Add X-Frame-Options header to response, depends on config settings
     *
     * @return $this
     */
    public function addDomainPolicyHeader(Varien_Event_Observer $observer)
    {
        $action = $observer->getControllerAction();
        $policy = null;

        if ($action->getLayout()->getArea() == 'adminhtml') {
            $policy = $this->getBackendPolicy();
        } elseif ($action->getLayout()->getArea() == 'frontend') {
            $policy = $this->getFrontendPolicy();
        }

        if ($policy) {
            $response = $action->getResponse();
            $response->setHeader('X-Frame-Options', $policy, true);
        }

        return $this;
    }

    /**
     * Get backend policy
     *
     * @return null|string
     */
    public function getBackendPolicy()
    {
        return $this->_getDomainPolicyByCode((int) (string) $this->_store->getConfig(self::XML_DOMAIN_POLICY_BACKEND));
    }

    /**
     * Get frontend policy
     *
     * @return null|string
     */
    public function getFrontendPolicy()
    {
        return $this->_getDomainPolicyByCode((int) (string) $this->_store->getConfig(self::XML_DOMAIN_POLICY_FRONTEND));
    }

    /**
     * Return string representation for policy code
     *
     * @param string $policyCode
     * @return null|string
     */
    protected function _getDomainPolicyByCode($policyCode)
    {
        return match ($policyCode) {
            self::FRAME_POLICY_ALLOW => null,
            default => 'SAMEORIGIN',
        };
    }
}
