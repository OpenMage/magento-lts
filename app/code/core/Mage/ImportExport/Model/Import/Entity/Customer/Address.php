<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_ImportExport
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Import entity customer address
 *
 * @category   Mage
 * @package    Mage_ImportExport
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_ImportExport_Model_Import_Entity_Customer_Address extends Mage_ImportExport_Model_Import_Entity_Abstract
{
    /**
     * Prefix for source file column name, which displays that column contains address data.
     */
    public const COL_NAME_PREFIX = '_address_';

    /**
     * Particular columns that contains of customer default addresses.
     */
    public const COL_NAME_DEFAULT_BILLING  = '_address_default_billing_';
    public const COL_NAME_DEFAULT_SHIPPING = '_address_default_shipping_';

    /**
     * Error codes.
     */
    public const ERROR_INVALID_REGION = 'invalidRegion';

    /**
     * Customer address attributes parameters.
     *
     *  [attr_code_1] => array(
     *      'options' => array(),
     *      'type' => 'text', 'price', 'textarea', 'select', etc.
     *      'id' => ..
     *  ),
     *  ...
     *
     * @var array
     */
    protected $_attributes = [];

    /**
     * Countrys and its regions.
     *
     * array(
     *   [country_id_lowercased_1] => array(
     *     [region_code_lowercased_1]         => region_id_1,
     *     [region_default_name_lowercased_1] => region_id_1,
     *     ...,
     *     [region_code_lowercased_n]         => region_id_n,
     *     [region_default_name_lowercased_n] => region_id_n
     *   ),
     *   ...
     * )
     *
     * @var array
     */
    protected $_countryRegions = [];

    /**
     * Customer import entity.
     *
     * @var Mage_ImportExport_Model_Import_Entity_Customer
     */
    protected $_customer;

    /**
     * Default addresses column names to appropriate customer attribute code.
     *
     * @var array
     */
    protected static $_defaultAddressAttrMapping = [
        self::COL_NAME_DEFAULT_BILLING  => 'default_billing',
        self::COL_NAME_DEFAULT_SHIPPING => 'default_shipping'
    ];

    /**
     * Customer entity DB table name.
     *
     * @var string
     */
    protected $_entityTable;

    /**
     * Attributes with index (not label) value.
     *
     * @var array
     */
    protected $_indexValueAttributes = ['country_id'];

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = [self::ERROR_INVALID_REGION => 'Region is invalid'];

    /**
     * Column names that holds values with particular meaning.
     *
     * @var array
     */
    protected $_particularAttributes = [self::COL_NAME_DEFAULT_BILLING, self::COL_NAME_DEFAULT_SHIPPING];

    /**
     * Region ID to region default name pairs.
     *
     * @var array
     */
    protected $_regions = [];

    /**
     * @param Mage_ImportExport_Model_Import_Entity_Customer $customer
     */
    public function __construct(Mage_ImportExport_Model_Import_Entity_Customer $customer)
    {
        parent::__construct();

        $this->_initAttributes()->_initCountryRegions();

        $this->_entityTable = Mage::getModel('customer/address')->getResource()->getEntityTable();
        $this->_customer    = $customer;

        foreach ($this->_messageTemplates as $errorCode => $message) {
            $this->_customer->addMessageTemplate($errorCode, $message);
        }
    }

    /**
     * Import data rows.
     *
     * @return bool
     */
    protected function _importData()
    {
        /** @var Mage_Customer_Model_Customer $customer */
        $customer       = Mage::getModel('customer/customer');
        /** @var Mage_Customer_Model_Address $resource */
        $resource       = Mage::getModel('customer/address');
        $strftimeFormat = Varien_Date::convertZendToStrftime(Varien_Date::DATETIME_INTERNAL_FORMAT, true, true);
        $table          = $resource->getResource()->getEntityTable();
        /** @var Mage_ImportExport_Model_Resource_Helper_Mysql4 $helper */
        $helper         = Mage::getResourceHelper('importexport');
        $nextEntityId   = $helper->getNextAutoincrement($table);
        $customerId     = null;
        $regionColName  = self::getColNameForAttrCode('region');
        $countryColName = self::getColNameForAttrCode('country_id');
        /** @var Mage_Customer_Model_Attribute $regionIdAttr */
        $regionIdAttr   = Mage::getSingleton('eav/config')->getAttribute($this->getEntityTypeCode(), 'region_id');
        $regionIdTable  = $regionIdAttr->getBackend()->getTable();
        $regionIdAttrId = $regionIdAttr->getId();
        $isAppendMode   = Mage_ImportExport_Model_Import::BEHAVIOR_APPEND == $this->_customer->getBehavior();
        $multiSelect    = [];

        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            $entityRows = [];
            $attributes = [];
            $defaults   = []; // customer default addresses (billing/shipping) data

            foreach ($bunch as $rowNum => $rowData) {
                $rowScope = $this->_getRowScope($rowData);
                if ($rowScope == Mage_ImportExport_Model_Import_Entity_Customer::SCOPE_DEFAULT) {
                    $customerId = $this->_customer->getCustomerId(
                        $rowData[Mage_ImportExport_Model_Import_Entity_Customer::COL_EMAIL],
                        $rowData[Mage_ImportExport_Model_Import_Entity_Customer::COL_WEBSITE]
                    );
                }
                if ($rowScope != Mage_ImportExport_Model_Import_Entity_Customer::SCOPE_OPTIONS) {
                    $multiSelect = [];
                }
                if (!$customerId) {
                    continue;
                }

                /** @var Mage_Customer_Model_Resource_Address_Collection $addressCollection */
                $addressCollection = Mage::getResourceModel('customer/address_collection');
                $addressCollection->addAttributeToFilter('parent_id', $customerId);

                $addressAttributes = [];
                foreach ($this->_attributes as $attrAlias => $attrParams) {
                    if (isset($rowData[$attrAlias]) && strlen($rowData[$attrAlias])) {
                        if ($attrParams['type'] === 'select') {
                            $value = $attrParams['options'][strtolower($rowData[$attrAlias])];
                        } elseif ($attrParams['type'] === 'datetime') {
                            $value = gmstrftime($strftimeFormat, strtotime($rowData[$attrAlias]));
                        } elseif ($attrParams['type'] === 'multiselect') {
                            $value = $attrParams['options'][strtolower($rowData[$attrAlias])];
                            $multiSelect[$attrParams['id']][] = $value;
                        } else {
                            $value = $rowData[$attrAlias];
                        }
                        $addressAttributes[$attrParams['id']] = $value;
                        $addressCollection->addAttributeToFilter($attrParams['code'], $value);
                    }
                }

                // skip duplicate address
                if ($isAppendMode && $addressCollection->getSize()) {
                    continue;
                }

                $entityId = $nextEntityId++;

                if ($rowScope == Mage_ImportExport_Model_Import_Entity_Customer::SCOPE_DEFAULT
                    || $rowScope == Mage_ImportExport_Model_Import_Entity_Customer::SCOPE_ADDRESS
                ) {
                    // entity table data
                    $now = Varien_Date::now();
                    $entityRows[] = [
                        'entity_id'      => $entityId,
                        'entity_type_id' => $this->_entityTypeId,
                        'parent_id'      => $customerId,
                        'created_at'     => $now,
                        'updated_at'     => $now
                    ];
                    // attribute values
                    foreach ($this->_attributes as $attrAlias => $attrParams) {
                        if (isset($addressAttributes[$attrParams['id']])) {
                            $attributes[$attrParams['table']][$entityId][$attrParams['id']]
                                = $addressAttributes[$attrParams['id']];
                        }
                    }
                    // customer default addresses
                    foreach (self::getDefaultAddressAttrMapping() as $colName => $customerAttrCode) {
                        if (!empty($rowData[$colName])) {
                            $attribute    = $customer->getAttribute($customerAttrCode);
                            $backendTable = $attribute->getBackend()->getTable();
                            $defaults[$backendTable][$customerId][$attribute->getId()] = $entityId;
                        }
                    }
                    // let's try to find region ID
                    if (!empty($rowData[$regionColName])) {
                        $countryNormalized = strtolower($rowData[$countryColName]);
                        $regionNormalized  = strtolower($rowData[$regionColName]);

                        if (isset($this->_countryRegions[$countryNormalized][$regionNormalized])) {
                            $regionId = $this->_countryRegions[$countryNormalized][$regionNormalized];
                            $attributes[$regionIdTable][$entityId][$regionIdAttrId] = $regionId;
                            // set 'region' attribute value as default name
                            $tbl             = $this->_attributes[$regionColName]['table'];
                            $regionColNameId = $this->_attributes[$regionColName]['id'];
                            $attributes[$tbl][$entityId][$regionColNameId] = $this->_regions[$regionId];
                        }
                    }
                } else {
                    foreach (array_intersect_key($rowData, $this->_attributes) as $attrCode => $value) {
                        $attrParams = $this->_attributes[$attrCode];
                        if ($attrParams['type'] === 'multiselect') {
                            $value = '';
                            if (isset($multiSelect[$attrParams['id']])) {
                                $value = implode(',', $multiSelect[$attrParams['id']]);
                            }
                            $attributes[$this->_attributes[$attrCode]['table']][$entityId][$attrParams['id']] = $value;
                        }
                    }
                }
            }
            $this->_saveAddressEntity($entityRows)
                ->_saveAddressAttributes($attributes)
                ->_saveCustomerDefaults($defaults);
        }
        return true;
    }

    /**
     * Initialize customer address attributes.
     *
     * @return $this
     */
    protected function _initAttributes()
    {
        $addrCollection = Mage::getResourceModel('customer/address_attribute_collection')
            ->addSystemHiddenFilter()
            ->addExcludeHiddenFrontendFilter();

        foreach ($addrCollection as $attribute) {
            $attributeCode = $attribute->getAttributeCode();
            $this->_attributes[self::getColNameForAttrCode($attributeCode)] = [
                'id'          => $attribute->getId(),
                'code'        => $attributeCode,
                'table'       => $attribute->getBackend()->getTable(),
                'is_required' => $attribute->getIsRequired(),
                'rules'       => $attribute->getValidateRules()
                    ? Mage::helper('core/unserializeArray')->unserialize($attribute->getValidateRules())
                    : null,
                'type'        => Mage_ImportExport_Model_Import::getAttributeType($attribute),
                'options'     => $this->getAttributeOptions($attribute)
            ];
        }
        return $this;
    }

    /**
     * Initialize country regions hash for clever recognition.
     *
     * @return $this
     */
    protected function _initCountryRegions()
    {
        foreach (Mage::getResourceModel('directory/region_collection') as $regionRow) {
            $countryNormalized = strtolower($regionRow['country_id']);
            $regionCode = strtolower($regionRow['code']);
            $regionName = strtolower($regionRow['default_name']);
            $this->_countryRegions[$countryNormalized][$regionCode] = $regionRow['region_id'];
            $this->_countryRegions[$countryNormalized][$regionName] = $regionRow['region_id'];
            $this->_regions[$regionRow['region_id']] = $regionRow['default_name'];
        }
        return $this;
    }

    /**
     * Check address data availability in row data.
     *
     * @param array $rowData
     * @return bool
     */
    protected function _isRowWithAddress(array $rowData)
    {
        foreach (array_keys($this->_attributes) as $colName) {
            if (isset($rowData[$colName]) && strlen($rowData[$colName])) {
                return true;
            }
        }
        return false;
    }

    /**
     * Save customer address attributes.
     *
     * @param array $attributesData
     * @return $this
     */
    protected function _saveAddressAttributes(array $attributesData)
    {
        foreach ($attributesData as $tableName => $data) {
            $tableData = [];

            foreach ($data as $addressId => $attrData) {
                foreach ($attrData as $attributeId => $value) {
                    $tableData[] = [
                        'entity_id'      => $addressId,
                        'entity_type_id' => $this->_entityTypeId,
                        'attribute_id'   => $attributeId,
                        'value'          => $value
                    ];
                }
            }
            $this->_connection->insertMultiple($tableName, $tableData);
        }
        return $this;
    }

    /**
     * Update and insert data in entity table.
     *
     * @param array $entityRows Rows for insert
     * @return $this
     */
    protected function _saveAddressEntity(array $entityRows)
    {
        if ($entityRows) {
            if (Mage_ImportExport_Model_Import::BEHAVIOR_APPEND != $this->_customer->getBehavior()) {
                $customersToClean = [];

                foreach ($entityRows as $entityData) {
                    $customersToClean[$entityData['parent_id']] = true;
                }
                $this->_connection->delete(
                    $this->_entityTable,
                    $this->_connection->quoteInto('`parent_id` IN (?)', array_keys($customersToClean))
                );
            }
            $this->_connection->insertMultiple($this->_entityTable, $entityRows);
        }
        return $this;
    }

    /**
     * Save customer default addresses.
     *
     * @param array $defaults
     * @return $this
     */
    protected function _saveCustomerDefaults(array $defaults)
    {
        foreach ($defaults as $tableName => $data) {
            $tableData = [];

            foreach ($data as $customerId => $attrData) {
                foreach ($attrData as $attributeId => $value) {
                    $tableData[] = [
                        'entity_id'      => $customerId,
                        'entity_type_id' => $this->_customer->getEntityTypeId(),
                        'attribute_id'   => $attributeId,
                        'value'          => $value
                    ];
                }
            }
            $this->_connection->insertOnDuplicate($tableName, $tableData, ['value']);
        }
        return $this;
    }

    /**
     * Get column name which holds value for attribute with specified code.
     *
     * @static
     * @param string $attrCode
     * @return string
     */
    public static function getColNameForAttrCode($attrCode)
    {
        return self::COL_NAME_PREFIX . $attrCode;
    }

    /**
     * Customer default addresses column name to customer attribute mapping array.
     *
     * @static
     * @return array
     */
    public static function getDefaultAddressAttrMapping()
    {
        return self::$_defaultAddressAttrMapping;
    }

    /**
     * EAV entity type code getter.
     *
     * @return string
     */
    public function getEntityTypeCode()
    {
        return 'customer_address';
    }

    /**
     * Is attribute contains particular data (not plain entity attribute).
     *
     * @param string $attrCode
     * @return bool
     */
    public function isAttributeParticular($attrCode)
    {
        return isset($this->_attributes[$attrCode]) || in_array($attrCode, $this->_particularAttributes);
    }

    /**
     * Validate data row.
     *
     * @param array $rowData
     * @param int $rowNum
     * @return bool
     */
    public function validateRow(array $rowData, $rowNum)
    {
        $rowIsValid = true;

        if ($this->_isRowWithAddress($rowData)) {
            foreach ($this->_attributes as $colName => $attrParams) {
                if (isset($rowData[$colName]) && strlen($rowData[$colName])) {
                    $rowIsValid &= $this->_customer->isAttributeValid($colName, $attrParams, $rowData, $rowNum);
                } elseif ($attrParams['is_required']) {
                    $this->_customer->addRowError(
                        Mage_ImportExport_Model_Import_Entity_Customer::ERROR_VALUE_IS_REQUIRED,
                        $rowNum,
                        $colName
                    );
                    $rowIsValid = false;
                }
            }
            // validate region for countries with known region list
            if ($rowIsValid) {
                $regionColName  = self::getColNameForAttrCode('region');
                $countryColName = self::getColNameForAttrCode('country_id');
                $countryRegions = $this->_countryRegions[strtolower($rowData[$countryColName])] ?? [];

                if (!empty($rowData[$regionColName])
                    && !empty($countryRegions)
                    && !isset($countryRegions[strtolower($rowData[$regionColName])])
                ) {
                    $this->_customer->addRowError(self::ERROR_INVALID_REGION, $rowNum);

                    $rowIsValid = false;
                }
            }
        }
        return $rowIsValid;
    }

    /**
     * Get current scope
     *
     * @param array $rowData
     * @return int
     */
    protected function _getRowScope($rowData)
    {
        if (strlen(trim($rowData[Mage_ImportExport_Model_Import_Entity_Customer::COL_EMAIL]))) {
            $scope = Mage_ImportExport_Model_Import_Entity_Customer::SCOPE_DEFAULT;
        } elseif (strlen(trim($rowData[Mage_ImportExport_Model_Import_Entity_Customer::COL_POSTCODE]))) {
            $scope = Mage_ImportExport_Model_Import_Entity_Customer::SCOPE_ADDRESS;
        } else {
            $scope = Mage_ImportExport_Model_Import_Entity_Customer::SCOPE_OPTIONS;
        }
        return $scope;
    }
}
