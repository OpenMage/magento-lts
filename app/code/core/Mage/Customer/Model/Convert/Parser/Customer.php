<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Customer
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Customer
 */
class Mage_Customer_Model_Convert_Parser_Customer extends Mage_Eav_Model_Convert_Parser_Abstract
{
    public const MULTI_DELIMITER = ' , ';

    protected $_resource;

    /**
     * Product collections per store
     *
     * @var array
     */
    protected $_collections;

    protected $_customerModel;
    protected $_customerAddressModel;
    protected $_newsletterModel;
    protected $_store;
    protected $_storeId;

    protected $_stores;

    /**
     * Website collection array
     *
     * @var array|null
     */
    protected $_websites;

    protected $_attributes = [];

    protected $_fields;

    /**
     * Array to contain customer groups
     * @var null|array
     */
    protected $_customerGroups = null;

    /**
     * @return Mage_Core_Model_Config_Element[]|SimpleXMLElement|null
     */
    public function getFields()
    {
        if (!$this->_fields) {
            $this->_fields = Mage::getConfig()->getFieldset('customer_dataflow', 'admin');
        }
        return $this->_fields;
    }

    /**
     * Retrieve customer model cache
     *
     * @return Mage_Customer_Model_Customer|object
     */
    public function getCustomerModel()
    {
        if (is_null($this->_customerModel)) {
            $object = Mage::getModel('customer/customer');
            $this->_customerModel = Mage::objects()->save($object);
        }
        return Mage::objects()->load($this->_customerModel);
    }

    /**
     * Retrieve customer address model cache
     *
     * @return Mage_Customer_Model_Address|object
     */
    public function getCustomerAddressModel()
    {
        if (is_null($this->_customerAddressModel)) {
            $object = Mage::getModel('customer/address');
            $this->_customerAddressModel = Mage::objects()->save($object);
        }
        return Mage::objects()->load($this->_customerAddressModel);
    }

    /**
     * Retrieve newsletter subscribers model cache
     *
     * @return Mage_Newsletter_Model_Subscriber|object
     */
    public function getNewsletterModel()
    {
        if (is_null($this->_newsletterModel)) {
            $object = Mage::getModel('newsletter/subscriber');
            $this->_newsletterModel = Mage::objects()->save($object);
        }
        return Mage::objects()->load($this->_newsletterModel);
    }

    /**
     * Retrieve current store model
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        if (is_null($this->_store)) {
            try {
                $store = Mage::app()->getStore($this->getVar('store'));
            } catch (Exception $e) {
                $this->addException(Mage::helper('catalog')->__('An invalid store was specified.'), Varien_Convert_Exception::FATAL);
                throw $e;
            }
            $this->_store = $store;
        }
        return $this->_store;
    }

    /**
     * Retrieve store ID
     *
     * @return int
     */
    public function getStoreId()
    {
        if (is_null($this->_storeId)) {
            $this->_storeId = $this->getStore()->getId();
        }
        return $this->_storeId;
    }

    /**
     * @param int $storeId
     * @return Mage_Core_Model_Store|bool
     */
    public function getStoreById($storeId)
    {
        if (is_null($this->_stores)) {
            $this->_stores = Mage::app()->getStores(true);
        }
        return $this->_stores[$storeId] ?? false;
    }

    /**
     * Retrieve website model by id
     *
     * @param int $websiteId
     * @return Mage_Core_Model_Website|false
     */
    public function getWebsiteById($websiteId)
    {
        if (is_null($this->_websites)) {
            $this->_websites = Mage::app()->getWebsites(true);
        }
        return $this->_websites[$websiteId] ?? false;
    }

    /**
     * Retrieve eav entity attribute model
     *
     * @param string $code
     * @return Mage_Eav_Model_Entity_Attribute
     */
    public function getAttribute($code)
    {
        if (!isset($this->_attributes[$code])) {
            $this->_attributes[$code] = $this->getCustomerModel()->getResource()->getAttribute($code);
        }
        return $this->_attributes[$code];
    }

    /**
     * @return Mage_Catalog_Model_Mysql4_Convert
     */
    public function getResource()
    {
        if (!$this->_resource) {
            $this->_resource = Mage::getResourceSingleton('catalog_entity/convert');
        }
        return $this->_resource;
    }

    /**
     * @param int $storeId
     * @return Mage_Customer_Model_Resource_Customer_Collection
     */
    public function getCollection($storeId)
    {
        if (!isset($this->_collections[$storeId])) {
            $this->_collections[$storeId] = Mage::getResourceModel('customer/customer_collection');
            $this->_collections[$storeId]->getEntity()->setStore($storeId);
        }
        return $this->_collections[$storeId];
    }

    /**
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function unparse()
    {
        $systemFields = [];
        foreach ($this->getFields() as $code => $node) {
            if ($node->is('system')) {
                $systemFields[] = $code;
            }
        }

        $entityIds = $this->getData();

        foreach ($entityIds as $i => $entityId) {
            /** @var Mage_Customer_Model_Customer $customer */
            $customer = $this->getCustomerModel()
                ->setData([])
                ->load($entityId);

            $position = Mage::helper('catalog')->__('Line %d, Email: %s', ($i + 1), $customer->getEmail());
            $this->setPosition($position);

            $row = [];

            foreach ($customer->getData() as $field => $value) {
                if ($field == 'website_id') {
                    $website = $this->getWebsiteById($value);
                    if ($website === false) {
                        $website = $this->getWebsiteById(0);
                    }
                    $row['website'] = $website->getCode();
                    continue;
                }

                if (in_array($field, $systemFields) || is_object($value)) {
                    continue;
                }

                $attribute = $this->getAttribute($field);
                if (!$attribute) {
                    continue;
                }

                if ($attribute->usesSource()) {
                    $option = $attribute->getSource()->getOptionText($value);
                    if ($value && empty($option)) {
                        $message = Mage::helper('catalog')->__('An invalid option ID is specified for %s (%s), skipping the record.', $field, $value);
                        $this->addException($message, Mage_Dataflow_Model_Convert_Exception::ERROR);
                        continue;
                    }
                    if (is_array($option)) {
                        $value = implode(self::MULTI_DELIMITER, $option);
                    } else {
                        $value = $option;
                    }
                    unset($option);
                } elseif (is_array($value)) {
                    continue;
                }
                $row[$field] = $value;
            }

            $defaultBillingId  = $customer->getDefaultBilling();
            $defaultShippingId = $customer->getDefaultShipping();

            $customerAddress = $this->getCustomerAddressModel();

            if (!$defaultBillingId) {
                foreach ($this->getFields() as $code => $node) {
                    if ($node->is('billing')) {
                        $row['billing_' . $code] = null;
                    }
                }
            } else {
                $customerAddress->load($defaultBillingId);

                foreach ($this->getFields() as $code => $node) {
                    if ($node->is('billing')) {
                        $row['billing_' . $code] = $customerAddress->getDataUsingMethod($code);
                    }
                }
            }

            if (!$defaultShippingId) {
                foreach ($this->getFields() as $code => $node) {
                    if ($node->is('shipping')) {
                        $row['shipping_' . $code] = null;
                    }
                }
            } else {
                if ($defaultShippingId != $defaultBillingId) {
                    $customerAddress->load($defaultShippingId);
                }
                foreach ($this->getFields() as $code => $node) {
                    if ($node->is('shipping')) {
                        $row['shipping_' . $code] = $customerAddress->getDataUsingMethod($code);
                    }
                }
            }

            $store = $this->getStoreById($customer->getStoreId());
            if ($store === false) {
                $store = $this->getStoreById(0);
            }
            $row['created_in'] = $store->getCode();

            $newsletter = $this->getNewsletterModel()
                ->setData([])
                ->loadByCustomer($customer);
            $row['is_subscribed'] = ($newsletter->getId()
                && $newsletter->getSubscriberStatus() == Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED)
                ? 1 : 0;

            if ($customer->getGroupId()) {
                $groupCode = $this->_getCustomerGroupCode($customer);
                if (is_null($groupCode)) {
                    $this->addException(
                        Mage::helper('catalog')->__('An invalid group ID is specified, skipping the record.'),
                        Mage_Dataflow_Model_Convert_Exception::ERROR,
                    );
                    continue;
                } else {
                    $row['group'] = $groupCode;
                }
            }

            $batchExport = $this->getBatchExportModel()
                ->setId(null)
                ->setBatchId($this->getBatchModel()->getId())
                ->setBatchData($row)
                ->setStatus(1)
                ->save();
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getExternalAttributes()
    {
        $internal = [
            'store_id',
            'entity_id',
            'website_id',
            'group_id',
            'created_in',
            'default_billing',
            'default_shipping',
            'country_id',
        ];

        $customerAttributes = Mage::getResourceModel('customer/attribute_collection')
            ->load()->getIterator();

        $addressAttributes = Mage::getResourceModel('customer/address_attribute_collection')
            ->load()->getIterator();

        $attributes = [
            'website'       => 'website',
            'email'         => 'email',
            'group'         => 'group',
            'create_in'     => 'create_in',
            'is_subscribed' => 'is_subscribed',
        ];

        foreach ($customerAttributes as $attr) {
            $code = $attr->getAttributeCode();
            if (in_array($code, $internal) || $attr->getFrontendInput() == 'hidden') {
                continue;
            }
            $attributes[$code] = $code;
        }
        $attributes['password_hash'] = 'password_hash';

        foreach ($addressAttributes as $attr) {
            $code = $attr->getAttributeCode();
            if (in_array($code, $internal) || $attr->getFrontendInput() == 'hidden') {
                continue;
            }

            if ($code == 'street') {
                $attributes['billing_' . $code . '_full'] = 'billing_' . $code;
            } else {
                $attributes['billing_' . $code] = 'billing_' . $code;
            }
        }
        $attributes['billing_country'] = 'billing_country';

        foreach ($addressAttributes as $attr) {
            $code = $attr->getAttributeCode();
            if (in_array($code, $internal) || $attr->getFrontendInput() == 'hidden') {
                continue;
            }

            if ($code == 'street') {
                $attributes['shipping_' . $code . '_full'] = 'shipping_' . $code;
            } else {
                $attributes['shipping_' . $code] = 'shipping_' . $code;
            }
        }
        $attributes['shipping_country'] = 'shipping_country';

        return $attributes;
    }

    /**
     * Gets group code by customer's groupId
     *
     * @param Mage_Customer_Model_Customer $customer
     * @return string|null
     */
    protected function _getCustomerGroupCode($customer)
    {
        if (is_null($this->_customerGroups)) {
            $groups = Mage::getResourceModel('customer/group_collection')
                    ->load();

            /** @var Mage_Customer_Model_Group $group */
            foreach ($groups as $group) {
                $this->_customerGroups[$group->getId()] = $group->getData('customer_group_code');
            }
        }

        return $this->_customerGroups[$customer->getGroupId()] ?? null;
    }

    /**
     * @deprecated not used anymore
     */
    public function parse()
    {
        $data = $this->getData();

        $entityTypeId = Mage::getSingleton('eav/config')->getEntityType('customer')->getId();
        $result = [];
        foreach ($data as $i => $row) {
            $this->setPosition('Line: ' . ($i + 1));
            try {
                // validate SKU
                if (empty($row['email'])) {
                    $this->addException(Mage::helper('customer')->__('Missing email, skipping the record.'), Varien_Convert_Exception::ERROR);
                    continue;
                }
                $this->setPosition('Line: ' . ($i + 1) . ', email: ' . $row['email']);

                // if attribute_set not set use default
                if (empty($row['attribute_set'])) {
                    $row['attribute_set'] = 'Default';
                }

                // get attribute_set_id, if not throw error
                $row['attribute_set_id'] = $this->getAttributeSetId($entityTypeId, $row['attribute_set']);
                if (!$row['attribute_set_id']) {
                    $this->addException(Mage::helper('customer')->__('Invalid attribute set specified, skipping the record.'), Varien_Convert_Exception::ERROR);
                    continue;
                }

                if (empty($row['group'])) {
                    $row['group'] = 'General';
                }

                if (empty($row['firstname'])) {
                    $this->addException(Mage::helper('customer')->__('Missing firstname, skipping the record.'), Varien_Convert_Exception::ERROR);
                    continue;
                }

                if (empty($row['lastname'])) {
                    $this->addException(Mage::helper('customer')->__('Missing lastname, skipping the record.'), Varien_Convert_Exception::ERROR);
                    continue;
                }

                // get store ids
                $storeIds = $this->getStoreIds($row['store'] ?? $this->getVar('store'));
                if (!$storeIds) {
                    $this->addException(Mage::helper('customer')->__('Invalid store specified, skipping the record.'), Varien_Convert_Exception::ERROR);
                    continue;
                }

                // import data
                $rowError = false;
                foreach ($storeIds as $storeId) {
                    $collection = $this->getCollection($storeId);
                    $entity = $collection->getEntity();

                    $model = Mage::getModel('customer/customer');
                    $model->setStoreId($storeId);
                    if (!empty($row['entity_id'])) {
                        $model->load($row['entity_id']);
                    }
                    foreach ($row as $field => $value) {
                        $attribute = $entity->getAttribute($field);
                        if (!$attribute) {
                            continue;
                            #$this->addException(Mage::helper('catalog')->__("Unknown attribute: %s.", $field), Varien_Convert_Exception::ERROR);
                        }

                        if ($attribute->usesSource()) {
                            $source = $attribute->getSource();
                            $optionId = $this->getSourceOptionId($source, $value);
                            if (is_null($optionId)) {
                                $rowError = true;
                                $this->addException(
                                    Mage::helper('customer')->__(
                                        'Invalid attribute option specified for attribute %s (%s), skipping the record.',
                                        $field,
                                        $value,
                                    ),
                                    Varien_Convert_Exception::ERROR,
                                );
                                continue;
                            }
                            $value = $optionId;
                        }
                        $model->setData($field, $value);
                    }//foreach ($row as $field=>$value)

                    $billingAddress = $model->getPrimaryBillingAddress();
                    /** @var Mage_Customer_Model_Customer $customer */
                    $customer = Mage::getModel('customer/customer')->load($model->getId());

                    if (!$billingAddress instanceof Mage_Customer_Model_Address) {
                        $billingAddress = Mage::getModel('customer/address');
                        if ($customer->getId() && $customer->getDefaultBilling()) {
                            $billingAddress->setId($customer->getDefaultBilling());
                        }
                    }

                    $regions = Mage::getResourceModel('directory/region_collection')
                        ->addRegionNameFilter($row['billing_region'])
                        ->load();
                    if ($regions) {
                        /** @var Mage_Directory_Model_Region $region */
                        foreach ($regions as $region) {
                            $regionId = $region->getId();
                        }
                    }

                    $billingAddress->setFirstname($row['firstname']);
                    $billingAddress->setLastname($row['lastname']);
                    $billingAddress->setCity($row['billing_city']);
                    $billingAddress->setRegion($row['billing_region']);
                    $billingAddress->setRegionId($regionId);
                    $billingAddress->setCountryId($row['billing_country']);
                    $billingAddress->setPostcode($row['billing_postcode']);
                    $billingAddress->setStreet([$row['billing_street1'],$row['billing_street2']]);
                    if (!empty($row['billing_telephone'])) {
                        $billingAddress->setTelephone($row['billing_telephone']);
                    }

                    if (!$model->getDefaultBilling()) {
                        $billingAddress->setCustomerId($model->getId());
                        $billingAddress->setIsDefaultBilling(true);
                        $billingAddress->save();
                        $model->setDefaultBilling($billingAddress->getId());
                        $model->addAddress($billingAddress);
                        if ($customer->getDefaultBilling()) {
                            $model->setDefaultBilling($customer->getDefaultBilling());
                        } else {
                            $billingAddress->save();
                            $model->setDefaultShipping($billingAddress->getId());
                            $model->addAddress($billingAddress);
                        }
                    }

                    $shippingAddress = $model->getPrimaryShippingAddress();
                    if (!$shippingAddress instanceof Mage_Customer_Model_Address) {
                        $shippingAddress = Mage::getModel('customer/address');
                        if ($customer->getId() && $customer->getDefaultShipping()) {
                            $shippingAddress->setId($customer->getDefaultShipping());
                        }
                    }

                    $regions = Mage::getResourceModel('directory/region_collection')
                        ->addRegionNameFilter($row['shipping_region'])
                        ->load();
                    if ($regions) {
                        foreach ($regions as $region) {
                            $regionId = $region->getId();
                        }
                    }

                    $shippingAddress->setFirstname($row['firstname']);
                    $shippingAddress->setLastname($row['lastname']);
                    $shippingAddress->setCity($row['shipping_city']);
                    $shippingAddress->setRegion($row['shipping_region']);
                    $shippingAddress->setRegionId($regionId);
                    $shippingAddress->setCountryId($row['shipping_country']);
                    $shippingAddress->setPostcode($row['shipping_postcode']);
                    $shippingAddress->setStreet([$row['shipping_street1'], $row['shipping_street2']]);
                    $shippingAddress->setCustomerId($model->getId());
                    if (!empty($row['shipping_telephone'])) {
                        $shippingAddress->setTelephone($row['shipping_telephone']);
                    }

                    if (!$model->getDefaultShipping()) {
                        if ($customer->getDefaultShipping()) {
                            $model->setDefaultShipping($customer->getDefaultShipping());
                        } else {
                            $shippingAddress->save();
                            $model->setDefaultShipping($shippingAddress->getId());
                            $model->addAddress($shippingAddress);
                        }
                        $shippingAddress->setIsDefaultShipping(true);
                    }

                    if (!$rowError) {
                        $collection->addItem($model);
                    }
                } //foreach ($storeIds as $storeId)
            } catch (Exception $e) {
                if (!$e instanceof Mage_Dataflow_Model_Convert_Exception) {
                    $this->addException(Mage::helper('customer')->__('An error occurred while retrieving the option value: %s.', $e->getMessage()), Mage_Dataflow_Model_Convert_Exception::FATAL);
                }
            }
        }
        $this->setData($this->_collections);
        return $this;
    }
}
