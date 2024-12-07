<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_ImportExport
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Export entity customer model
 *
 * @category   Mage
 * @package    Mage_ImportExport
 */
class Mage_ImportExport_Model_Export_Entity_Customer extends Mage_ImportExport_Model_Export_Entity_Abstract
{
    /**
     * Permanent column names.
     *
     * Names that begins with underscore is not an attribute. This name convention is for
     * to avoid interference with same attribute name.
     */
    public const COL_EMAIL   = 'email';
    public const COL_WEBSITE = '_website';
    public const COL_STORE   = '_store';

    /**
     * Overridden attributes parameters.
     *
     * @var array
     */
    protected $_attributeOverrides = [
        'created_at'                  => ['backend_type' => 'datetime'],
        'reward_update_notification'  => ['source_model' => 'eav/entity_attribute_source_boolean'],
        'reward_warning_notification' => ['source_model' => 'eav/entity_attribute_source_boolean']
    ];

    /**
     * Array of attributes codes which are disabled for export.
     *
     * @var array
     */
    protected $_disabledAttrs = ['default_billing', 'default_shipping'];

    /**
     * Attributes with index (not label) value.
     *
     * @var array
     */
    protected $_indexValueAttributes = ['group_id', 'website_id', 'store_id'];

    /**
     * Permanent entity columns.
     *
     * @var array
     */
    protected $_permanentAttributes = [self::COL_EMAIL, self::COL_WEBSITE, self::COL_STORE];

    public function __construct()
    {
        parent::__construct();

        $this->_initAttrValues()
                ->_initStores()
                ->_initWebsites();
    }

    /**
     * Initialize website values.
     *
     * @return $this
     */
    protected function _initWebsites()
    {
        foreach (Mage::app()->getWebsites(true) as $website) {
            $this->_websiteIdToCode[$website->getId()] = $website->getCode();
        }
        return $this;
    }

    /**
     * Apply filter to collection and add not skipped attributes to select.
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function _prepareEntityCollection(Mage_Eav_Model_Entity_Collection_Abstract $collection)
    {
        // forced addition default billing and shipping addresses attributes
        return parent::_prepareEntityCollection($collection)->addAttributeToSelect(
            Mage_ImportExport_Model_Import_Entity_Customer_Address::getDefaultAddressAttrMapping()
        );
    }

    /**
     * Export process and return contents of temporary file
     *
     * @deprecated after ver 1.9.2.4 use $this->exportFile() instead
     *
     * @return string
     */
    public function export()
    {
        $this->_prepareExport();

        return $this->getWriter()->getContents();
    }

    /**
     * Export process and return temporary file through array
     *
     * This method will return following array:
     *
     * array(
     *     'rows'  => count of written rows,
     *     'value' => path to created file
     * )
     *
     * @return array
     */
    public function exportFile()
    {
        $this->_prepareExport();

        $writer = $this->getWriter();

        return [
            'rows'  => $writer->getRowsCount(),
            'value' => $writer->getDestination()
        ];
    }

    /**
     * Prepare data for export and write its to temporary file through writer.
     */
    protected function _prepareExport()
    {
        $collection = $this->_prepareEntityCollection(Mage::getResourceModel('customer/customer_collection'));
        $validAttrCodes = $this->_getExportAttrCodes();
        $writer         = $this->getWriter();
        $defaultAddrMap = Mage_ImportExport_Model_Import_Entity_Customer_Address::getDefaultAddressAttrMapping();

        // prepare address data
        $allAddressAttributeOptions   = [];
        $addrColNames                 = [];
        $customerAddrs                = [];
        $addressAttributeCollection   = Mage::getResourceModel('customer/address_attribute_collection')
            ->addSystemHiddenFilter()
            ->addExcludeHiddenFrontendFilter();
        $addressAttributes            = [];
        $addrAttributeMultiSelect     = [];
        $customerAttributeMultiSelect = [];

        foreach ($addressAttributeCollection as $attribute) {
            $attrCode = $attribute->getAttributeCode();
            $allAddressAttributeOptions[$attrCode] = $this->_getAddressAttributeOptions($attribute);
            $addrColNames[] = Mage_ImportExport_Model_Import_Entity_Customer_Address::getColNameForAttrCode($attrCode);
        }
        foreach (Mage::getResourceModel('customer/address_collection')->addAttributeToSelect('*') as $address) {
            $addrRow = [];

            if (empty($addressAttributes)) {
                $addressAttributes = $address->getAttributes();
            }
            foreach ($allAddressAttributeOptions as $attrCode => $attrValues) {
                $column = Mage_ImportExport_Model_Import_Entity_Customer_Address::getColNameForAttrCode($attrCode);
                if ($address->getData($attrCode) !== null) {
                    if (!isset($addressAttributes[$attrCode])) {
                        $addressAttributes = array_merge($addressAttributes, $address->getAttributes());
                    }
                    $addressAttribute = $addressAttributes[$attrCode];
                    $value            = $address->getData($attrCode);

                    if ($addressAttribute->getFrontendInput() == 'multiselect') {
                        $optionIds   = explode(',', $value);
                        $optionTexts = [];
                        foreach ($optionIds as $optionId) {
                            $optionText             = $addressAttribute->getSource()->getOptionText($optionId);
                            $optionTexts[$optionId] = $optionText;
                        }
                        $addrAttributeMultiSelect[$address['parent_id']][$address->getId()][$column] = $optionTexts;
                    } elseif ($attrValues) {
                        $value = $attrValues[$value];
                    }
                    $addrRow[$column] = $value;
                }
            }
            $customerAddrs[$address['parent_id']][$address->getId()] = $addrRow;
        }

        // create export file
        $writer->setHeaderCols(array_merge(
            $this->_permanentAttributes,
            $validAttrCodes,
            ['password'],
            $addrColNames,
            array_keys($defaultAddrMap)
        ));
        foreach ($collection as $customerId => $customer) {
            $customerAddress = $customerAddrs[$customerId] ?? [];
            $addressMultiselect = $addrAttributeMultiSelect[$customerId] ?? [];

            $row          = $this->_prepareExportRow($customer, $customerAttributeMultiSelect);
            $defaultAddrs = $this->_prepareDefaultAddress($customer);

            $addrRow          = [];
            $currentAddressId = 0;
            if (isset($customerAddrs[$customerId])) {
                list($addressId, $addrRow) = $this->_getNextAddressRow($customerAddress);
                $row              = $this->_addDefaultAddressFields($defaultAddrs, $addressId, $row);
                $addrRow          = $this->_addNextAddressOptions($addressMultiselect, $addressId, $addrRow);
                $currentAddressId = $addressId;
            }
            foreach ($customerAttributeMultiSelect as $column => &$multiSelectOptions) {
                $row[$column] = array_shift($multiSelectOptions);
            }
            $writeRow = array_merge($row, $addrRow);
            $writer->writeRow($writeRow);

            $additionalRowsCount = $this->_getAdditionalRowsCount(
                $customerAddress,
                $addressMultiselect,
                $customerAttributeMultiSelect
            );
            if ($additionalRowsCount) {
                for ($i = 0; $i < $additionalRowsCount; $i++) {
                    $writeRow = [];

                    foreach ($customerAttributeMultiSelect as $column => &$multiSelectOptions) {
                        $writeRow[$column] = array_shift($multiSelectOptions);
                    }
                    if (!$this->_isExistMultiSelectOptions($addressMultiselect, $currentAddressId)) {
                        list($addressId, $addrRow) = $this->_getNextAddressRow($customerAddress);
                        $currentAddressId = $addressId;
                        $addrRow = $this->_addNextAddressOptions($addressMultiselect, $currentAddressId, $addrRow);
                    } else {
                        $addrRow = [];
                        $addrRow = $this->_addNextAddressOptions($addressMultiselect, $currentAddressId, $addrRow);
                    }

                    if ($addrRow) {
                        $writeRow = array_merge($writeRow, $addrRow);
                    }
                    $writer->writeRow($writeRow);
                }
            }
        }
    }

    /**
     * Get Additional Rows Count
     *
     * @param array $customerAddress
     * @param array $addrMultiSelect
     * @param array $customerMultiSelect
     * @return int
     */
    protected function _getAdditionalRowsCount($customerAddress, $addrMultiSelect, $customerMultiSelect)
    {
        $additionalRowsCount = count($customerAddress);
        $addressRowCount     = 0;
        $allAddressRowCount  = [];

        foreach ($addrMultiSelect as $addressId => $addressAttributeOptions) {
            foreach ($addressAttributeOptions as $options) {
                $addressRowCount                = max(count($options), $addressRowCount);
                $allAddressRowCount[$addressId] = $addressRowCount;
            }
            $addressRowCount = 0;
        }

        $additionalRowsCount = max(array_sum($allAddressRowCount), $additionalRowsCount);

        foreach ($customerMultiSelect as $options) {
            $additionalRowsCount = max(count($options), $additionalRowsCount);
        }

        return $additionalRowsCount;
    }

    /**
     * Get Next Address Row
     *
     * @param array $customerAddress
     * @return array
     */
    protected function _getNextAddressRow(&$customerAddress)
    {
        if (!empty($customerAddress)) {
            reset($customerAddress);
            $addressId  = key($customerAddress);
            $addressRow = current($customerAddress);
            unset($customerAddress[$addressId]);

            return [$addressId, $addressRow];
        }
        return [null, null];
    }

    /**
     * Clean up already loaded attribute collection.
     *
     * @return Mage_Eav_Model_Resource_Entity_Attribute_Collection
     */
    public function filterAttributeCollection(Mage_Eav_Model_Resource_Entity_Attribute_Collection $collection)
    {
        foreach (parent::filterAttributeCollection($collection) as $attribute) {
            if (!empty($this->_attributeOverrides[$attribute->getAttributeCode()])) {
                $data = $this->_attributeOverrides[$attribute->getAttributeCode()];

                if (isset($data['options_method']) && method_exists($this, $data['options_method'])) {
                    $data['filter_options'] = $this->{$data['options_method']}();
                }
                $attribute->addData($data);
            }
        }
        return $collection;
    }

    /**
     * Entity attributes collection getter.
     *
     * @return Mage_Customer_Model_Resource_Attribute_Collection|Object
     */
    public function getAttributeCollection()
    {
        return Mage::getResourceModel('customer/attribute_collection');
    }

    /**
     * EAV entity type code getter.
     *
     * @return string
     */
    public function getEntityTypeCode()
    {
        return 'customer';
    }

    /**
     * Get Address Attributes
     *
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @return array
     */
    protected function _getAddressAttributeOptions($attribute)
    {
        $options  = [];
        $attrCode = $attribute->getAttributeCode();

        if ($attribute->usesSource() && $attrCode != 'country_id') {
            foreach ($attribute->getSource()->getAllOptions(false) as $option) {
                $innerOptions = is_array($option['value']) ? $option['value'] : [$option];
                foreach ($innerOptions as $innerOption) {
                    // skip ' -- Please Select -- ' option
                    if (strlen($innerOption['value'])) {
                        $options[$innerOption['value']] = $innerOption['label'];
                    }
                }
            }
        }
        return $options;
    }

    /**
     * Prepare Export Row
     *
     * @param Mage_Customer_Model_Customer $customer
     * @param array $attributeMultiSelect
     * @return array
     */
    protected function _prepareExportRow($customer, &$attributeMultiSelect)
    {
        $row            = [];
        $validAttrCodes = $this->_getExportAttrCodes();

        // go through all valid attribute codes
        foreach ($validAttrCodes as $attrCode) {
            $attribute = $customer->getAttribute($attrCode);
            $attrValue = $customer->getData($attrCode);

            if ($attribute && $attribute->getFrontendInput() == 'multiselect') {
                $optionText = (array)$attribute->getSource()->getOptionText($attrValue);
                if ($optionText) {
                    $attributeMultiSelect[$attrCode] = $optionText;
                    $attrValue                       = null;
                }
            } elseif (isset($this->_attributeValues[$attrCode])
                && isset($this->_attributeValues[$attrCode][$attrValue])
            ) {
                $attrValue = $this->_attributeValues[$attrCode][$attrValue];
            }
            if ($attrValue !== null) {
                $row[$attrCode] = $attrValue;
            }
        }
        $row[self::COL_WEBSITE] = $this->_websiteIdToCode[$customer['website_id'] ?? 0];
        $row[self::COL_STORE]   = $this->_storeIdToCode[$customer['store_id']];

        return $row;
    }

    /**
     * Prepare Default Address
     *
     * @param Mage_Customer_Model_Customer $customer
     * @return array
     */
    protected function _prepareDefaultAddress($customer)
    {
        $defaultAddrMap = Mage_ImportExport_Model_Import_Entity_Customer_Address::getDefaultAddressAttrMapping();
        $defaultAddrs   = [];

        foreach ($defaultAddrMap as $colName => $addrAttrCode) {
            if (!empty($customer[$addrAttrCode])) {
                $defaultAddrs[$customer[$addrAttrCode]][] = $colName;
            }
        }
        return $defaultAddrs;
    }

    /**
     * Add default fields to row
     *
     * @param array $defaultAddrs
     * @param int $addressId
     * @param array $row
     * @return array
     */
    protected function _addDefaultAddressFields($defaultAddrs, $addressId, $row)
    {
        if (isset($defaultAddrs[$addressId])) {
            foreach ($defaultAddrs[$addressId] as $colName) {
                $row[$colName] = 1;
            }
            return $row;
        }
        return $row;
    }

    /**
     * Get Next Address MultiSelect option
     *
     * @param array $addrAttributeMultiSelect
     * @param int $addressId
     * @param array $addrRow
     * @return array
     */
    protected function _addNextAddressOptions(&$addrAttributeMultiSelect, $addressId, $addrRow)
    {
        if (!isset($addrAttributeMultiSelect[$addressId])) {
            return $addrRow;
        }
        $addrMultiSelectOption = &$addrAttributeMultiSelect[$addressId];
        if (is_array($addrMultiSelectOption)) {
            foreach ($addrMultiSelectOption as $column => &$options) {
                $addrRow[$column] = array_shift($options);
            }
        }
        return $addrRow;
    }

    /**
     * Check if exist MultiSelect Options
     *
     * @param array $addrAttributeMultiSelect
     * @param int $addressId
     * @return bool
     */
    protected function _isExistMultiSelectOptions($addrAttributeMultiSelect, $addressId)
    {
        $result = false;
        if (!isset($addrAttributeMultiSelect[$addressId])) {
            return $result;
        }
        $addrMultiSelectOption = $addrAttributeMultiSelect[$addressId];
        if (is_array($addrMultiSelectOption)) {
            foreach ($addrMultiSelectOption as $option) {
                if (!empty($option)) {
                    $result = true;
                    break;
                }
            }
        }
        return $result;
    }
}
