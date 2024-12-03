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
 * Webservice api2 config model
 *
 * @category   Mage
 * @package    Mage_Api2
 */
class Mage_Api2_Model_Config extends Varien_Simplexml_Config
{
    /**
     * Node name of resource groups
     */
    public const NODE_RESOURCE_GROUPS = 'resource_groups';

    /**
     * Id for config cache
     */
    public const CACHE_ID  = 'config_api2';

    /**
     * Tag name for config cache
     */
    public const CACHE_TAG = 'CONFIG_API2';

    /**
     * Is resources added to group
     *
     * @var bool
     */
    protected $_resourcesGrouped = false;

    /**
     * Constructor
     * Initializes XML for this configuration
     * Local cache configuration
     *
     * @param string|Varien_Simplexml_Element|null $sourceData
     */
    public function __construct($sourceData = null)
    {
        parent::__construct($sourceData);

        $canUserCache = Mage::app()->useCache('config');
        if ($canUserCache) {
            $this->setCacheId(self::CACHE_ID)
                ->setCacheTags([self::CACHE_TAG])
                ->setCacheChecksum(null)
                ->setCache(Mage::app()->getCache());

            if ($this->loadCache()) {
                return;
            }
        }

        // Load data of config files api2.xml
        $config = Mage::getConfig()->loadModulesConfiguration('api2.xml');
        $this->setXml($config->getNode('api2'));

        if ($canUserCache) {
            $this->saveCache();
        }
    }

    /**
     * Fetch all routes of the given api type from config files api2.xml
     *
     * @param string $apiType
     * @throws Mage_Api2_Exception
     * @return array
     */
    public function getRoutes($apiType)
    {
        /** @var Mage_Api2_Helper_Data $helper */
        $helper = Mage::helper('api2');
        if (!$helper->isApiTypeSupported($apiType)) {
            throw new Mage_Api2_Exception(
                sprintf('API type "%s" is not supported', $apiType),
                Mage_Api2_Model_Server::HTTP_BAD_REQUEST
            );
        }

        $routes = [];
        foreach ($this->getResources() as $resourceKey => $resource) {
            if (!$resource->routes) {
                continue;
            }

            /** @var Varien_Simplexml_Element $route */
            foreach ($resource->routes->children() as $route) {
                $arguments = [
                    Mage_Api2_Model_Route_Abstract::PARAM_ROUTE    => (string)$route->route,
                    Mage_Api2_Model_Route_Abstract::PARAM_DEFAULTS => [
                        'model'       => (string)$resource->model,
                        'type'        => (string)$resourceKey,
                        'action_type' => (string)$route->action_type
                    ]
                ];

                $routes[] = Mage::getModel('api2/route_' . $apiType, $arguments);
            }
        }
        return $routes;
    }

    /**
     * Retrieve all resources from config files api2.xml
     *
     * @return SimpleXMLElement|Varien_Simplexml_Element
     */
    public function getResources()
    {
        return $this->getNode('resources')->children();
    }

    /**
     * Retrieve all resources types
     *
     * @return array
     */
    public function getResourcesTypes()
    {
        $list = [];

        foreach ($this->getResources() as $resourceType => $resourceCfg) {
            $list[] = (string) $resourceType;
        }
        return $list;
    }

    /**
     * Retrieve all resource groups from config files api2.xml
     *
     * @return Varien_Simplexml_Element|false
     */
    public function getResourceGroups()
    {
        $groups = $this->getXpath('//' . self::NODE_RESOURCE_GROUPS);
        if (!$groups) {
            return false;
        }

        /** @var Varien_Simplexml_Element $groups */
        $groups = $groups[0];

        if (!$this->_resourcesGrouped) {
            /** @var Varien_Simplexml_Element $node */
            foreach ($this->getResources() as $node) {
                $result = $node->xpath('group');
                if (!$result) {
                    continue;
                }
                $groupName = (string) $result[0];
                if ($groupName) {
                    $result = $groups->xpath('.//' . $groupName);
                    if (!$result) {
                        continue;
                    }

                    /** @var Varien_Simplexml_Element $group */
                    $group = $result[0];

                    $children = $group->children ?? new Varien_Simplexml_Element('<children />');
                    $node->resource = 1;
                    $children->appendChild($node);
                    $group->appendChild($children);
                }
            }
        }
        return $groups;
    }

    /**
     * Retrieve resource group from config files api2.xml
     *
     * @param string $name
     * @return Mage_Core_Model_Config_Element|boolean
     */
    public function getResourceGroup($name)
    {
        $group = $this->getResourceGroups()->xpath('.//' . $name);
        if (!$group) {
            return false;
        }
        return $group[0];
    }

    /**
     * Retrieve resource by type (node)
     *
     * @param string $node
     * @return Varien_Simplexml_Element|boolean
     */
    public function getResource($node)
    {
        return $this->getNode('resources/' . $node);
    }

    /**
     * Retrieve resource attributes
     *
     * @param string $node
     * @return array
     */
    public function getResourceAttributes($node)
    {
        $attributes = $this->getNode('resources/' . $node . '/attributes');
        return $attributes ? $attributes->asCanonicalArray() : [];
    }

    /**
     * Get excluded attributes of API resource
     *
     * @param string $resource
     * @param string $userType
     * @param string $operation
     * @return array
     */
    public function getResourceExcludedAttributes($resource, $userType, $operation)
    {
        $node = $this->getNode('resources/' . $resource . '/exclude_attributes/' . $userType . '/' . $operation);
        $exclAttributes = [];

        if ($node) {
            foreach ($node->children() as $attribute => $status) {
                if ((string) $status) {
                    $exclAttributes[] = $attribute;
                }
            }
        }
        return $exclAttributes;
    }

    /**
     * Get forced attributes of API resource
     *
     * @param string $resource
     * @param string $userType
     * @return array
     */
    public function getResourceForcedAttributes($resource, $userType)
    {
        $node = $this->getNode('resources/' . $resource . '/force_attributes/' . $userType);
        $forcedAttributes = [];

        if ($node) {
            foreach ($node->children() as $attribute => $status) {
                if ((string) $status) {
                    $forcedAttributes[] = $attribute;
                }
            }
        }
        return $forcedAttributes;
    }

    /**
     * Get included attributes
     *
     * @param string $resource API resource ID
     * @param string $userType API user type
     * @param string $operationType Type of operation: one of Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_... constant
     * @return array
     */
    public function getResourceIncludedAttributes($resource, $userType, $operationType)
    {
        $node = $this->getNode('resources/' . $resource . '/include_attributes/' . $userType . '/' . $operationType);
        $inclAttributes = [];

        if ($node) {
            foreach ($node->children() as $attribute => $status) {
                if ((string) $status) {
                    $inclAttributes[] = $attribute;
                }
            }
        }
        return $inclAttributes;
    }

    /**
     * Get entity only attributes
     *
     * @param string $resource API resource ID
     * @param string $userType API user type
     * @param string $operationType Type of operation: one of Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_... constant
     * @return array
     */
    public function getResourceEntityOnlyAttributes($resource, $userType, $operationType)
    {
        $node = $this->getNode('resources/' . $resource . '/entity_only_attributes/' . $userType . '/' .
            $operationType);
        $entityOnlyAttributes = [];

        if ($node) {
            foreach ($node->children() as $attribute => $status) {
                if ((string) $status) {
                    $entityOnlyAttributes[] = $attribute;
                }
            }
        }
        return $entityOnlyAttributes;
    }

    /**
     * Retrieve resource working model
     *
     * @param string $node
     * @return string
     */
    public function getResourceWorkingModel($node)
    {
        return (string)$this->getNode('resources/' . $node . '/working_model');
    }

    /**
     * Get resource allowed versions sorted in reverse order
     *
     * @param string $node
     * @return array
     * @throws Exception
     */
    public function getVersions($node)
    {
        $element = $this->getNode('resources/' . $node . '/versions');
        if (!$element) {
            throw new Exception(
                sprintf('Resource "%s" does not have node <versions> in config.', htmlspecialchars($node))
            );
        }

        $versions = explode(',', (string)$element);
        if (count(array_filter($versions, 'is_numeric')) != count($versions)) {
            throw new Exception(sprintf('Invalid resource "%s" versions in config.', htmlspecialchars($node)));
        }

        rsort($versions, SORT_NUMERIC);

        return $versions;
    }

    /**
     * Retrieve resource model
     *
     * @param string $node
     * @return string
     */
    public function getResourceModel($node)
    {
        return (string)$this->getNode('resources/' . $node . '/model');
    }

    /**
     * Retrieve API user privileges for specified resource
     *
     * @param string $resource
     * @param string $userType
     * @return array
     */
    public function getResourceUserPrivileges($resource, $userType)
    {
        $attributes = $this->getNode('resources/' . $resource . '/privileges/' . $userType);
        return $attributes ? $attributes->asCanonicalArray() : [];
    }

    /**
     * Retrieve resource subresources
     *
     * @param string $node
     * @return array
     */
    public function getResourceSubresources($node)
    {
        $subresources = $this->getNode('resources/' . $node . '/subresources');
        return $subresources ? $subresources->asCanonicalArray() : [];
    }

    /**
     * Get validation config by validator type
     *
     * @param string $resourceType
     * @param string $validatorType
     * @return array
     */
    public function getValidationConfig($resourceType, $validatorType)
    {
        $config = $this->getNode('resources/' . $resourceType . '/validators/' . $validatorType);
        return $config ? $config->asCanonicalArray() : [];
    }

    /**
     * Get latest version of resource model. If second arg is specified - use it as a limiter
     *
     * @param string $resourceType Resource type
     * @param int $lowerOrEqualsTo OPTIONAL If specified - return version equal or lower to
     * @return int
     */
    public function getResourceLastVersion($resourceType, $lowerOrEqualsTo = null)
    {
        $availVersions = $this->getVersions($resourceType); // already ordered in reverse order
        $useVersion    = reset($availVersions);

        if ($lowerOrEqualsTo !== null) {
            foreach ($availVersions as $availVersion) {
                if ($availVersion <= $lowerOrEqualsTo) {
                    $useVersion = $availVersion;
                    break;
                }
            }
        }
        return (int)$useVersion;
    }

    /**
     * Get route with Mage_Api2_Model_Resource::ACTION_TYPE_ENTITY type
     *
     * @param string $node
     * @return string
     */
    public function getRouteWithEntityTypeAction($node)
    {
        return (string)$this->getNode('resources/' . $node . '/routes/route_entity/route');
    }
}
