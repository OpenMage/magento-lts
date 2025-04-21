<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api2
 */

/**
 * API2 filter ACL attribute resources permissions model
 *
 * @package    Mage_Api2
 */
class Mage_Api2_Model_Acl_Filter_Attribute_ResourcePermission implements Mage_Api2_Model_Acl_PermissionInterface
{
    /**
     * @var array
     */
    protected $_resourcesPermissions;

    /**
     * Filter item value
     *
     * @var string
     */
    protected $_userType;

    /**
     * Flag if resource has entity only attributes
     *
     * @var bool
     */
    protected $_hasEntityOnlyAttributes = false;

    /**
     * Get resources permissions for selected role
     *
     * @return array
     */
    public function getResourcesPermissions()
    {
        if ($this->_resourcesPermissions === null) {
            $rulesPairs = [];

            if ($this->_userType) {
                $allowedAttributes = [];

                /** @var Mage_Api2_Model_Resource_Acl_Filter_Attribute_Collection $rules */
                $rules = Mage::getResourceModel('api2/acl_filter_attribute_collection');
                $rules->addFilterByUserType($this->_userType);

                foreach ($rules as $rule) {
                    if (Mage_Api2_Model_Acl_Global_Rule::RESOURCE_ALL === $rule->getResourceId()) {
                        $rulesPairs[$rule->getResourceId()] = Mage_Api2_Model_Acl_Global_Rule_Permission::TYPE_ALLOW;
                    }

                    /** @var Mage_Api2_Model_Acl_Filter_Attribute $rule */
                    if ($rule->getAllowedAttributes() !== null) {
                        $allowedAttributes[$rule->getResourceId()][$rule->getOperation()] = explode(
                            ',',
                            $rule->getAllowedAttributes(),
                        );
                    }
                }

                /** @var Mage_Api2_Model_Config $config */
                $config = Mage::getModel('api2/config');

                /** @var Mage_Api2_Model_Acl_Filter_Attribute_Operation $operationSource */
                $operationSource = Mage::getModel('api2/acl_filter_attribute_operation');

                foreach ($config->getResourcesTypes() as $resource) {
                    $resourceUserPrivileges = $config->getResourceUserPrivileges($resource, $this->_userType);

                    if (!$resourceUserPrivileges) { // skip user without any privileges for resource
                        continue;
                    }
                    $operations = $operationSource::toArray();

                    if (empty($resourceUserPrivileges[Mage_Api2_Model_Resource::OPERATION_CREATE])
                        && empty($resourceUserPrivileges[Mage_Api2_Model_Resource::OPERATION_UPDATE])
                    ) {
                        unset($operations[Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_WRITE]);
                    }
                    if (empty($resourceUserPrivileges[Mage_Api2_Model_Resource::OPERATION_RETRIEVE])) {
                        unset($operations[Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_READ]);
                    }
                    if (!$operations) { // skip resource without any operations allowed
                        continue;
                    }
                    try {
                        /** @var Mage_Api2_Model_Resource $resourceModel */
                        $resourceModel = Mage::getModel($config->getResourceModel($resource));
                        if ($resourceModel) {
                            $resourceModel->setResourceType($resource)
                                ->setUserType($this->_userType);

                            foreach (array_keys($operations) as $operation) {
                                if (!$this->_hasEntityOnlyAttributes
                                    && $config->getResourceEntityOnlyAttributes($resource, $this->_userType, $operation)
                                ) {
                                    $this->_hasEntityOnlyAttributes = true;
                                }
                                $availableAttributes = $resourceModel->getAvailableAttributes(
                                    $this->_userType,
                                    $operation,
                                );
                                asort($availableAttributes);
                                foreach ($availableAttributes as $attribute => $attributeLabel) {
                                    $status = isset($allowedAttributes[$resource][$operation])
                                        && in_array($attribute, $allowedAttributes[$resource][$operation])
                                            ? Mage_Api2_Model_Acl_Global_Rule_Permission::TYPE_ALLOW
                                            : Mage_Api2_Model_Acl_Global_Rule_Permission::TYPE_DENY;

                                    $rulesPairs[$resource]['operations'][$operation]['attributes'][$attribute] = [
                                        'status'    => $status,
                                        'title'     => $attributeLabel,
                                    ];
                                }
                            }
                        }
                    } catch (Exception $e) {
                        // getModel() throws exception when application is in development mode
                        Mage::logException($e);
                    }
                }
            }
            $this->_resourcesPermissions = $rulesPairs;
        }
        return $this->_resourcesPermissions;
    }

    /**
     * Set filter value
     *
     * Set user type
     *
     * @param string $userType
     * @return $this
     */
    public function setFilterValue($userType)
    {
        if (!array_key_exists($userType, Mage_Api2_Model_Auth_User::getUserTypes())) {
            throw new Exception('Unknown user type.');
        }
        $this->_userType = $userType;
        return $this;
    }

    /**
     * Get flag value
     *
     * @return bool
     */
    public function getHasEntityOnlyAttributes()
    {
        return $this->_hasEntityOnlyAttributes;
    }
}
