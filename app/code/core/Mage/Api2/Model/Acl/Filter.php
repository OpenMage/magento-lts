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
 * API ACL filter
 *
 * @category   Mage
 * @package    Mage_Api2
 */
class Mage_Api2_Model_Acl_Filter
{
    /**
     * Attributes allowed for use
     *
     * @var array
     */
    protected $_allowedAttributes;

    /**
     * A list of attributes to be included into output
     *
     * @var array
     */
    protected $_attributesToInclude;

    /**
     * Associated resource model
     *
     * @var Mage_Api2_Model_Resource
     */
    protected $_resource;

    /**
     * Object constructor
     */
    public function __construct(Mage_Api2_Model_Resource $resource)
    {
        $this->_resource = $resource;
    }

    /**
     * Return only the data which keys are allowed
     *
     * @param array $allowedAttributes List of attributes available to use
     * @param array $data Associative array attribute to value
     * @return array
     */
    protected function _filter(array $allowedAttributes, array $data)
    {
        foreach (array_keys($data) as $attribute) {
            if (!in_array($attribute, $allowedAttributes)) {
                unset($data[$attribute]);
            }
        }
        return $data;
    }

    /**
     * Strip attributes in of collection items
     *
     * @param array $items
     * @return array
     */
    public function collectionIn($items)
    {
        foreach ($items as &$data) {
            $data = is_array($data) ? $this->in($data) : [];
        }
        return $items;
    }

    /**
     * Strip attributes out of collection items
     *
     * @param array $items
     * @return array
     */
    public function collectionOut($items)
    {
        foreach ($items as &$data) {
            $data = $this->out($data);
        }
        return $items;
    }

    /**
     * Fetch array of allowed attributes for given resource type, operation and user type.
     *
     * @param string $operationType OPTIONAL One of Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_... constant
     * @return array
     */
    public function getAllowedAttributes($operationType = null)
    {
        if ($this->_allowedAttributes === null) {
            /** @var Mage_Api2_Helper_Data $helper */
            $helper = Mage::helper('api2/data');

            if ($operationType === null) {
                $operationType = $helper->getTypeOfOperation($this->_resource->getOperation());
            }
            if ($helper->isAllAttributesAllowed($this->_resource->getUserType())) {
                $this->_allowedAttributes = array_keys($this->_resource->getAvailableAttributes(
                    $this->_resource->getUserType(),
                    $operationType
                ));
            } else {
                $this->_allowedAttributes = $helper->getAllowedAttributes(
                    $this->_resource->getUserType(),
                    $this->_resource->getResourceType(),
                    $operationType
                );
            }
            // force attributes to be no filtered
            foreach ($this->_resource->getForcedAttributes() as $forcedAttr) {
                if (!in_array($forcedAttr, $this->_allowedAttributes)) {
                    $this->_allowedAttributes[] = $forcedAttr;
                }
            }
        }
        return $this->_allowedAttributes;
    }

    /**
     * Retrieve a list of attributes to be included in output based on available and requested attributes
     *
     * @return array
     */
    public function getAttributesToInclude()
    {
        if ($this->_attributesToInclude === null) {
            $allowedAttrs   = $this->getAllowedAttributes(Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_READ);
            $requestedAttrs = $this->_resource->getRequest()->getRequestedAttributes();

            if ($requestedAttrs) {
                foreach ($allowedAttrs as $allowedAttr) {
                    if (in_array($allowedAttr, $requestedAttrs)) {
                        $this->_attributesToInclude[] = $allowedAttr;
                    }
                }
            } else {
                $this->_attributesToInclude = $allowedAttrs;
            }
        }
        return $this->_attributesToInclude;
    }

    /**
     * Filter data for write operations
     *
     * @return array
     * @SuppressWarnings(PHPMD.ShortMethodName)
     */
    public function in(array $requestData)
    {
        $allowedAttributes = $this->getAllowedAttributes(Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_WRITE);

        return $this->_filter($allowedAttributes, $requestData);
    }

    /**
     * Filter data before output
     *
     * @return array
     */
    public function out(array $retrievedData)
    {
        return $this->_filter($this->getAttributesToInclude(), $retrievedData);
    }
}
