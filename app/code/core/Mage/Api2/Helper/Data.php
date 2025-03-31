<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Api2
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Webservice API2 data helper
 *
 * @category   Mage
 * @package    Mage_Api2
 */
class Mage_Api2_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Request interpret adapters
     */
    public const XML_PATH_API2_REQUEST_INTERPRETERS = 'global/api2/request/interpreters';

    /**
     * Response render adapters
     */
    public const XML_PATH_API2_RESPONSE_RENDERS     = 'global/api2/response/renders';

    /**
     * Config paths
     */
    public const XML_PATH_AUTH_ADAPTERS = 'global/api2/auth_adapters';
    public const XML_PATH_USER_TYPES    = 'global/api2/user_types';

    protected $_moduleName = 'Mage_Api2';

    /**
     * Compare order to be used in adapters list sort
     *
     * @param array $a
     * @param array $b
     * @return int
     */
    protected static function _compareOrder($a, $b)
    {
        return $a['order'] <=> $b['order'];
    }

    /**
     * Retrieve Auth adapters info from configuration file as array
     *
     * @param bool $enabledOnly
     * @return array
     */
    public function getAuthAdapters($enabledOnly = false)
    {
        $adapters = Mage::getConfig()->getNode(self::XML_PATH_AUTH_ADAPTERS);

        if (!$adapters) {
            return [];
        }
        $adapters = $adapters->asArray();

        if ($enabledOnly) {
            foreach ($adapters as $adapter) {
                if (empty($adapter['enabled'])) {
                    unset($adapters);
                }
            }
            $adapters = (array) $adapters;
        }
        uasort($adapters, ['Mage_Api2_Helper_Data', '_compareOrder']);

        return $adapters;
    }

    /**
     * Retrieve enabled user types in form of user type => user model pairs
     *
     * @return array
     */
    public function getUserTypes()
    {
        $userModels = [];
        $types = Mage::getConfig()->getNode(self::XML_PATH_USER_TYPES);

        if ($types) {
            foreach ($types->asArray() as $type => $params) {
                if (!empty($params['allowed'])) {
                    $userModels[$type] = $params['model'];
                }
            }
        }
        return $userModels;
    }

    /**
     * Get interpreter type for Request body according to Content-type HTTP header
     *
     * @return array
     */
    public function getRequestInterpreterAdapters()
    {
        return (array) Mage::app()->getConfig()->getNode(self::XML_PATH_API2_REQUEST_INTERPRETERS);
    }

    /**
     * Get interpreter type for Request body according to Content-type HTTP header
     *
     * @return array
     */
    public function getResponseRenderAdapters()
    {
        return (array) Mage::app()->getConfig()->getNode(self::XML_PATH_API2_RESPONSE_RENDERS);
    }

    /**
     * Check API type support
     *
     * @param string $type
     * @return bool
     */
    public function isApiTypeSupported($type)
    {
        return in_array($type, Mage_Api2_Model_Server::getApiTypes());
    }

    /**
     * Get allowed attributes of a rule
     *
     * @param string $userType
     * @param string $resourceId
     * @param Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_* $operation
     * @return array
     */
    public function getAllowedAttributes($userType, $resourceId, $operation)
    {
        /** @var Mage_Api2_Model_Resource_Acl_Filter_Attribute $resource */
        $resource = Mage::getResourceModel('api2/acl_filter_attribute');

        $attributes = $resource->getAllowedAttributes($userType, $resourceId, $operation);

        return ($attributes === false || $attributes === null ? [] : explode(',', $attributes));
    }

    /**
     * Check if ALL attributes are allowed
     *
     * @param string $userType
     * @return bool
     */
    public function isAllAttributesAllowed($userType)
    {
        /** @var Mage_Api2_Model_Resource_Acl_Filter_Attribute $resource */
        $resource = Mage::getResourceModel('api2/acl_filter_attribute');

        return $resource->isAllAttributesAllowed($userType);
    }

    /**
     * Get operation type for specified operation
     *
     * @param Mage_Api2_Model_Resource::OPERATION_* $operation
     * @return Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_*
     * @throws Exception
     */
    public function getTypeOfOperation($operation)
    {
        if (Mage_Api2_Model_Resource::OPERATION_RETRIEVE === $operation) {
            return Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_READ;
        } elseif (Mage_Api2_Model_Resource::OPERATION_CREATE === $operation) {
            return Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_WRITE;
        } elseif (Mage_Api2_Model_Resource::OPERATION_UPDATE === $operation) {
            return Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_WRITE;
        } else {
            throw new Exception('Can not determine operation type');
        }
    }
}
