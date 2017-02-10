<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Magethrow
 * @package    Mage_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Domainpolicy
{
    /**
     * X-Frame-Options allow (header is absent)
     */
    const FRAME_POLICY_ALLOW = 1;

    /**
     * X-Frame-Options SAMEORIGIN
     */
    const FRAME_POLICY_ORIGIN = 2;

    /**
     * Path to backend domain policy settings
     */
    const XML_DOMAIN_POLICY_BACKEND = 'admin/security/domain_policy_backend';

    /**
     * Path to frontend domain policy settings
     */
    const XML_DOMAIN_POLICY_FRONTEND = 'admin/security/domain_policy_frontend';

    /**
     * Current store
     *
     * @var Mage_Core_Model_Store
     */
    protected $_store;

    public function __construct($options = array())
    {
        $this->_store = isset($options['store']) ? $options['store'] : Mage::app()->getStore();
    }

    /**
     * Add X-Frame-Options header to response, depends on config settings
     *
     * @var Varien_Object $observer
     * @return $this
     */
    public function addDomainPolicyHeader($observer)
    {
        /** @var Mage_Core_Controller->getCurrentAreaDomainPolicy_Varien_Action $action */
        $action = $observer->getControllerAction();
        $policy = null;

        if ('adminhtml' == $action->getLayout()->getArea()) {
            $policy = $this->getBackendPolicy();
        } elseif('frontend' == $action->getLayout()->getArea()) {
            $policy = $this->getFrontendPolicy();
        }

        if ($policy) {
            /** @var Mage_Core_Controller_Response_Http $response */
            $response = $action->getResponse();
            $response->setHeader('X-Frame-Options', $policy, true);
        }

        return $this;
    }

    /**
     * Get backend policy
     *
     * @return string|null
     */
    public function getBackendPolicy()
    {
        return $this->_getDomainPolicyByCode((int)(string)$this->_store->getConfig(self::XML_DOMAIN_POLICY_BACKEND));
    }

    /**
     * Get frontend policy
     *
     * @return string|null
     */
    public function getFrontendPolicy()
    {
        return $this->_getDomainPolicyByCode((int)(string)$this->_store->getConfig(self::XML_DOMAIN_POLICY_FRONTEND));
    }



    /**
     * Return string representation for policy code
     *
     * @param $policyCode
     * @return string|null
     */
    protected function _getDomainPolicyByCode($policyCode)
    {
        switch($policyCode) {
            case self::FRAME_POLICY_ALLOW:
                $policy = null;
                break;
            default:
                $policy = 'SAMEORIGIN';
        }

        return $policy;
    }
}
