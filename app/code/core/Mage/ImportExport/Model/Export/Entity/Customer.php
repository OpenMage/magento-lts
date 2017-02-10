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
 * @package     Mage_ImportExport
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Export entity customer model
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_ImportExport_Model_Export_Entity_Customer extends Mage_ImportExport_Model_Export_Entity_Abstract
{
    /**
     * Permanent column names.
     *
     * Names that begins with underscore is not an attribute. This name convention is for
     * to avoid interference with same attribute name.
     */
    const COL_EMAIL   = 'email';
    const COL_WEBSITE = '_website';
    const COL_STORE   = '_store';

    /**
     * Overriden attributes parameters.
     *
     * @var array
     */
    protected $_attributeOverrides = array(
        'created_at'                  => array('backend_type' => 'datetime'),
        'reward_update_notification'  => array('source_model' => 'eav/entity_attribute_source_boolean'),
        'reward_warning_notification' => array('source_model' => 'eav/entity_attribute_source_boolean')
    );

    /**
     * Array of attributes codes which are disabled for export.
     *
     * @var array
     */
    protected $_disabledAttrs = array('default_billing', 'default_shipping');

    /**
     * Attributes with index (not label) value.
     *
     * @var array
     */
    protected $_indexValueAttributes = array('group_id', 'website_id', 'store_id');

    /**
     * Permanent entity columns.
     *
     * @var array
     */
    protected $_permanentAttributes = array(self::COL_EMAIL, self::COL_WEBSITE, self::COL_STORE);

    /**
     * Constructor.
     *
     * @return void
     */
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
     * @return Mage_ImportExport_Model_Export_Entity_Customer
     */
    protected function _initWebsites()
    {
        /** @var $website Mage_Core_Model_Website */
        foreach (Mage::app()->getWebsites(true) as $website) {
            $this->_websiteIdToCode[$website->getId()] = $website->getCode();
        }
        return $this;
    }

    /**
     * Apply filter to collection and add not skipped attributes to select.
     *
     * @param Mage_Eav_Model_Entity_Collection_Abstract $collection
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

        return array(
            'rows'  => $writer->getRowsCount(),
            'value' => $writer->getDestination()
        );
    }

    /**
     * Prepare data for export and write its to temporary file through writer.
     *
     * @return void
     */
    protected function _prepareExport()
    {
        $collection = $this->_prepareEntityCollection(Mage::getResourceModel('customer/customer_collection'));
        $validAttrCodes = $this->_getExportAttrCodes();
        $writer         = $this->getWriter();
        $defaultAddrMap = Mage_ImportExport_Model_Import_Entity_Customer_Address::getDefaultAddressAttrMapping();

        // prepare address data
        $allAddressAttributeOptions   = array();
        $addrColNames                 = array();
        $customerAddrs                = array();
        $addressAttributeCollection   = Mage::getResourceModel('customer/address_attribute_collection')
            ->addSystemHiddenFilter()
            ->addExcludeHiddenFrontendFilter();
        $addressAttributes            = array();
        $addrAttributeMultiSelect     = array();
        $customerAttributeMultiSelect = array();

        foreach ($addressAttributeCollection as $attribute) {
            $attrCode = $attribute->getAttributeCode();
            $allAddressAttributeOptions[$attrCode] = $this->_getAddressAttributeOptions($attribute);
            $addrColNames[] = Mage_ImportExport_Model_Import_Entity_Customer_Address::getColNameForAttrCode($attrCode);
        }
        foreach (Mage::getResourceModel('customer/address_collection')->addAttributeToSelect('*') as $address) {
            $addrRow = array();

            if (empty($addressAttributes)) {
                $addressAttributes = $address->getAttributes();
            }
            foreach ($allAddressAttributeOptions as $attrCode => $attrValues) {
                $column = Mage_ImportExport_Model_Import_Entity_Customer_Address::getColNameForAttrCode($attrCode);
                if (null !== $address->getData($attrCode)) {
                    if (!isset($addressAttributes[$attrCode])) {
                        $addressAttributes = array_merge($addressAttributes, $address->getAttributes());
                    }
                    $addressAttribute = $addressAttributes[$attrCode];
                    $value            = $address->getData($attrCode);

                    if ($addressAttribute->getFrontendInput() == 'multiselect') {
                        $optionIds   = explode(',', $value);
                        $optionTexts = array();
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
            $this->_permanentAttributes, $validAttrCodes,
            array('password'), $addrColNames,
            array_keys($defaultAddrMap)
        ));
        foreach ($collection as $customerId => $customer) {
            $customerAddress = array();
            if (isset($customerAddrs[$customerId])) {
                $customerAddress = $customerAddrs[$customerId];
            }
            $addressMultiselect= array();
            if (isset($addrAttributeMultiSelect[$customerId])) {
                $addressMultiselect = $addrAttributeMultiSelect[$customerId];
            }

            $row          = $this->_prepareExportRow($customer, $customerAttributeMultiSelect);
            $defaultAddrs = $this->_prepareDefaultAddress($customer);

            $addrRow          = array();
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

            $additionalRowsCount = $this->_getAdditionalRowsCount($customerAddress,
                $addressMultiselect, $customerAttributeMultiSelect);
            if ($additionalRowsCount) {
                for ($i = 0; $i < $additionalRowsCount; $i++) {
                    $writeRow = array();

                    foreach ($customerAttributeMultiSelect as $column => &$multiSelectOptions) {
                        $writeRow[$column] = array_shift($multiSelectOptions);
                    }
                    if (!$this->_isExistMultiSelectOptions($addressMultiselect, $currentAddressId)) {
                        list($addressId, $addrRow) = $this->_getNextAddressRow($customerAddress);
                        $currentAddressId = $addressId;
                        $addrRow = $this->_addNextAddressOptions($addressMultiselect, $currentAddressId, $addrRow);
                    } else {
                        $addrRow = array();
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
        $allAddressRowCount  = array();

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

            return array($addressId, $addressRow);
        }
        return array(null, null);
    }

    /**
     * Clean up already loaded attribute collection.
     *
     * @param Mage_Eav_Model_Resource_Entity_Attribute_Collection $collection
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
     * @return Mage_Customer_Model_Entity_Attribute_Collection
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
     * @param $attribute
     * @return array
     */
    protected function _getAddressAttributeOptions($attribute)
    {
        $options  = array();
        $attrCode = $attribute->getAttributeCode();

        if ($attribute->usesSource() && 'country_id' != $attrCode) {
            foreach ($attribute->getSource()->getAllOptions(false) as $option) {
                $innerOptions = is_array($option['value']) ? $option['value'] : array($option);
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
        $row            = array();
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
            if (null !== $attrValue) {
                $row[$attrCode] = $attrValue;
            }
        }
        $row[self::COL_WEBSITE] = $this->_websiteIdToCode[$customer['website_id']];
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
        $defaultAddrs   = array();

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
     * @param $defaultAddrs
     * @param $addressId
     * @param $row
     * @return mixed
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
