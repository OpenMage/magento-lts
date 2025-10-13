<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_ImportExport
 */

/**
 * Import entity customer model
 *
 * @package    Mage_ImportExport
 */
class Mage_ImportExport_Model_Import_Entity_Customer extends Mage_ImportExport_Model_Import_Entity_Abstract
{
    /**
     * Size of bunch - part of entities to save in one step.
     */
    public const BUNCH_SIZE = 20;

    /**
     * Data row scopes.
     */
    public const SCOPE_DEFAULT = 1;

    public const SCOPE_ADDRESS = -1;

    public const SCOPE_OPTIONS = 2;

    /**
     * Permanent column names.
     *
     * Names that begins with underscore is not an attribute. This name convention is for
     * to avoid interference with same attribute name.
     */
    public const COL_EMAIL    = 'email';

    public const COL_WEBSITE  = '_website';

    public const COL_STORE    = '_store';

    public const COL_POSTCODE = '_address_postcode';

    /**
     * Error codes.
     */
    public const ERROR_INVALID_WEBSITE      = 'invalidWebsite';

    public const ERROR_INVALID_EMAIL        = 'invalidEmail';

    public const ERROR_DUPLICATE_EMAIL_SITE = 'duplicateEmailSite';

    public const ERROR_EMAIL_IS_EMPTY       = 'emailIsEmpty';

    public const ERROR_ROW_IS_ORPHAN        = 'rowIsOrphan';

    public const ERROR_VALUE_IS_REQUIRED    = 'valueIsRequired';

    public const ERROR_INVALID_STORE        = 'invalidStore';

    public const ERROR_EMAIL_SITE_NOT_FOUND = 'emailSiteNotFound';

    public const ERROR_PASSWORD_LENGTH      = 'passwordLength';

    /**
     * Customer constants
     *
     */
    public const DEFAULT_GROUP_ID = 1;

    public const MAX_PASSWD_LENGTH = 6;

    /**
     * Customer address import entity model.
     *
     * @var Mage_ImportExport_Model_Import_Entity_Customer_Address
     */
    protected $_addressEntity;

    /**
     * Customer attributes parameters.
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
     * MultiSelect Attributes
     *
     * @var array
     */
    protected $_multiSelectAttributes = [];

    /**
     * Customer account sharing. TRUE - is global, FALSE - is per website.
     *
     * @var bool
     */
    protected $_customerGlobal;

    /**
     * Customer groups ID-to-name.
     *
     * @var array
     */
    protected $_customerGroups = [];

    /**
     * Customer entity DB table name.
     *
     * @var string
     */
    protected $_entityTable;

    /**
     * Array of attribute codes which will be ignored in validation and import procedures.
     * For example, when entity attribute has own validation and import procedures
     * or just to deny this attribute processing.
     *
     * @var array
     */
    protected $_ignoredAttributes = ['website_id', 'store_id', 'default_billing', 'default_shipping'];

    /**
     * Attributes with index (not label) value.
     *
     * @var array
     */
    protected $_indexValueAttributes = ['group_id'];

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = [
        self::ERROR_INVALID_WEBSITE      => 'Invalid value in Website column (website does not exists?)',
        self::ERROR_INVALID_EMAIL        => 'E-mail is invalid',
        self::ERROR_DUPLICATE_EMAIL_SITE => 'E-mail is duplicated in import file',
        self::ERROR_EMAIL_IS_EMPTY       => 'E-mail is not specified',
        self::ERROR_ROW_IS_ORPHAN        => 'Orphan rows that will be skipped due default row errors',
        self::ERROR_VALUE_IS_REQUIRED    => "Required attribute '%s' has an empty value",
        self::ERROR_INVALID_STORE        => 'Invalid value in Store column (store does not exists?)',
        self::ERROR_EMAIL_SITE_NOT_FOUND => 'E-mail and website combination is not found',
        self::ERROR_PASSWORD_LENGTH      => 'Invalid password length',
    ];

    /**
     * Dry-ran customers information from import file.
     *
     * @var array
     */
    protected $_newCustomers = [];

    /**
     * Existing customers information. In form of:
     *
     * [customer e-mail] => array(
     *    [website code 1] => customer_id 1,
     *    [website code 2] => customer_id 2,
     *           ...       =>     ...      ,
     *    [website code n] => customer_id n,
     * )
     *
     * @var array
     */
    protected $_oldCustomers = [];

    /**
     * Column names that holds values with particular meaning.
     *
     * @var array
     */
    protected $_particularAttributes = [self::COL_WEBSITE, self::COL_STORE];

    /**
     * Permanent entity columns.
     *
     * @var array
     */
    protected $_permanentAttributes = [self::COL_EMAIL, self::COL_WEBSITE];

    /**
     * All stores code-ID pairs.
     *
     * @var array
     */
    protected $_storeCodeToId = [];

    /**
     * Website code-to-ID
     *
     * @var array
     */
    protected $_websiteCodeToId = [];

    /**
     * Website ID-to-code
     *
     * @var array
     */
    protected $_websiteIdToCode = [];

    public function __construct()
    {
        parent::__construct();

        $this->_initWebsites()
            ->_initStores()
            ->_initCustomerGroups()
            ->_initAttributes()
            ->_initCustomers();

        $this->_entityTable   = Mage::getModel('customer/customer')->getResource()->getEntityTable();
        $this->_addressEntity = Mage::getModel('importexport/import_entity_customer_address', $this);
    }

    /**
     * Delete customers.
     *
     * @return $this
     */
    protected function _deleteCustomers()
    {
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            $idToDelete = [];

            foreach ($bunch as $rowNum => $rowData) {
                if (self::SCOPE_DEFAULT == $this->getRowScope($rowData) && $this->validateRow($rowData, $rowNum)) {
                    $idToDelete[] = $this->_oldCustomers[$rowData[self::COL_EMAIL]][$rowData[self::COL_WEBSITE]];
                }
            }

            if ($idToDelete) {
                $this->_connection->query(
                    $this->_connection->quoteInto(
                        "DELETE FROM `{$this->_entityTable}` WHERE `entity_id` IN (?)",
                        $idToDelete,
                    ),
                );
            }
        }

        return $this;
    }

    /**
     * Save customer data to DB.
     *
     * @throws Exception
     * @return bool Result of operation.
     */
    protected function _importData()
    {
        if (Mage_ImportExport_Model_Import::BEHAVIOR_DELETE == $this->getBehavior()) {
            $this->_deleteCustomers();
        } else {
            $this->_saveCustomers();
            $this->_addressEntity->importData();
        }

        return true;
    }

    /**
     * Initialize customer attributes.
     *
     * @return $this
     */
    protected function _initAttributes()
    {
        $collection = Mage::getResourceModel('customer/attribute_collection')->addSystemHiddenFilterWithPasswordHash();
        /** @var Mage_Eav_Model_Attribute $attribute */
        foreach ($collection as $attribute) {
            $attributeArray = [
                'id'          => $attribute->getId(),
                'is_required' => $attribute->getIsRequired(),
                'is_static'   => $attribute->isStatic(),
                'rules'       => $attribute->getValidateRules()
                    ? Mage::helper('core/unserializeArray')->unserialize($attribute->getValidateRules())
                    : null,
                'type'        => Mage_ImportExport_Model_Import::getAttributeType($attribute),
                'options'     => $this->getAttributeOptions($attribute),
            ];
            $this->_attributes[$attribute->getAttributeCode()] = $attributeArray;
            if (Mage_ImportExport_Model_Import::getAttributeType($attribute) === 'multiselect') {
                $this->_multiSelectAttributes[$attribute->getAttributeCode()] = $attributeArray;
            }
        }

        return $this;
    }

    /**
     * Initialize customer groups.
     *
     * @return $this
     */
    protected function _initCustomerGroups()
    {
        foreach (Mage::getResourceModel('customer/group_collection') as $customerGroup) {
            $this->_customerGroups[$customerGroup->getId()] = true;
        }

        return $this;
    }

    /**
     * Initialize existent customers data.
     *
     * @return $this
     */
    protected function _initCustomers()
    {
        foreach (Mage::getResourceModel('customer/customer_collection') as $customer) {
            $email = $customer->getEmail();

            if (!isset($this->_oldCustomers[$email])) {
                $this->_oldCustomers[$email] = [];
            }

            $this->_oldCustomers[$email][$this->_websiteIdToCode[$customer->getWebsiteId()]] = $customer->getId();
        }

        $this->_customerGlobal = Mage::getModel('customer/customer')->getSharingConfig()->isGlobalScope();

        return $this;
    }

    /**
     * Initialize stores hash.
     *
     * @return $this
     */
    protected function _initStores()
    {
        foreach (Mage::app()->getStores(true) as $store) {
            $this->_storeCodeToId[$store->getCode()] = $store->getId();
        }

        return $this;
    }

    /**
     * Initialize website values.
     *
     * @return $this
     */
    protected function _initWebsites()
    {
        foreach (Mage::app()->getWebsites(true) as $website) {
            $this->_websiteCodeToId[$website->getCode()] = $website->getId();
            $this->_websiteIdToCode[$website->getId()]   = $website->getCode();
        }

        return $this;
    }

    /**
     * Gather and save information about customer entities.
     *
     * @return $this
     */
    protected function _saveCustomers()
    {
        /** @var Mage_Customer_Model_Customer $resource */
        $resource       = Mage::getModel('customer/customer');
        $table = $resource->getResource()->getEntityTable();
        /** @var Mage_ImportExport_Model_Resource_Helper_Mysql4 $helper */
        $helper         = Mage::getResourceHelper('importexport');
        $nextEntityId   = $helper->getNextAutoincrement($table);
        $passId         = $resource->getAttribute('password_hash')->getId();
        $passTable      = $resource->getAttribute('password_hash')->getBackend()->getTable();
        $multiSelect    = [];

        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            $entityRowsIn = [];
            $entityRowsUp = [];
            $attributes   = [];
            $entityId     = null;

            $oldCustomersToLower = array_change_key_case($this->_oldCustomers, CASE_LOWER);

            foreach ($bunch as $rowNum => $rowData) {
                if (!$this->validateRow($rowData, $rowNum)) {
                    continue;
                }

                if (self::SCOPE_DEFAULT == $this->getRowScope($rowData)) {
                    // entity table data
                    $now = Varien_Date::now();
                    $entityRow = [
                        'group_id'   => empty($rowData['group_id']) ? self::DEFAULT_GROUP_ID : $rowData['group_id'],
                        'store_id'   => empty($rowData[self::COL_STORE])
                                        ? 0 : $this->_storeCodeToId[$rowData[self::COL_STORE]],
                        'created_at' => empty($rowData['created_at'])
                                        ? $now : gmdate(Varien_Date::DATETIME_PHP_FORMAT, strtotime($rowData['created_at'])),
                        'updated_at' => $now,
                    ];

                    $emailToLower = strtolower($rowData[self::COL_EMAIL]);
                    if (isset($oldCustomersToLower[$emailToLower][$rowData[self::COL_WEBSITE]])) { // edit
                        $entityId = $oldCustomersToLower[$emailToLower][$rowData[self::COL_WEBSITE]];
                        $entityRow['entity_id'] = $entityId;
                        $entityRowsUp[] = $entityRow;
                    } else { // create
                        $entityId                      = $nextEntityId++;
                        $entityRow['entity_id']        = $entityId;
                        $entityRow['entity_type_id']   = $this->_entityTypeId;
                        $entityRow['attribute_set_id'] = 0;
                        $entityRow['website_id']       = $this->_websiteCodeToId[$rowData[self::COL_WEBSITE]];
                        $entityRow['email']            = $rowData[self::COL_EMAIL];
                        $entityRow['is_active']        = 1;
                        $entityRowsIn[]                = $entityRow;

                        $this->_newCustomers[$rowData[self::COL_EMAIL]][$rowData[self::COL_WEBSITE]] = $entityId;
                    }

                    // attribute values
                    foreach (array_intersect_key($rowData, $this->_attributes) as $attrCode => $value) {
                        if (!$this->_attributes[$attrCode]['is_static'] && strlen($value)) {
                            /** @var Mage_Customer_Model_Attribute $attribute */
                            $attribute  = $resource->getAttribute($attrCode);
                            $backModel  = $attribute->getBackendModel();
                            $attrParams = $this->_attributes[$attrCode];

                            if ($attrParams['type'] === 'select') {
                                $value = $attrParams['options'][strtolower($value)];
                            } elseif ($attrParams['type'] === 'datetime') {
                                $value = gmdate(Varien_Date::DATETIME_PHP_FORMAT, strtotime($value));
                            } elseif ($attrParams['type'] === 'multiselect') {
                                $value = (array) $attrParams['options'][strtolower($value)];
                                $attribute->getBackend()->beforeSave($resource->setData($attrCode, $value));
                                $value = $resource->getData($attrCode);
                                $multiSelect[$entityId][] = $value;
                            } elseif ($backModel) {
                                $attribute->getBackend()->beforeSave($resource->setData($attrCode, $value));
                                $value = $resource->getData($attrCode);
                            }

                            $attributes[$attribute->getBackend()->getTable()][$entityId][$attrParams['id']] = $value;

                            // restore 'backend_model' to avoid default setting
                            $attribute->setBackendModel($backModel);
                        }
                    }

                    // password change/set
                    if (isset($rowData['password']) && strlen($rowData['password'])) {
                        $attributes[$passTable][$entityId][$passId] = $resource->hashPassword($rowData['password']);
                    }
                } elseif (self::SCOPE_OPTIONS == $this->getRowScope($rowData)) {
                    foreach (array_intersect_key($rowData, $this->_attributes) as $attrCode => $value) {
                        $attribute  = $resource->getAttribute($attrCode);
                        $attrParams = $this->_attributes[$attrCode];
                        if ($attrParams['type'] === 'multiselect') {
                            if (!isset($attrParams['options'][strtolower($value)])) {
                                continue;
                            }

                            $value = $attrParams['options'][strtolower($value)];
                            if (isset($multiSelect[$entityId])) {
                                $multiSelect[$entityId][] = $value;
                                $value = $multiSelect[$entityId];
                            }

                            $attribute->getBackend()->beforeSave($resource->setData($attrCode, $value));
                            $value = $resource->getData($attrCode);
                            $attributes[$attribute->getBackend()->getTable()][$entityId][$attrParams['id']] = $value;
                        }
                    }
                }
            }

            $this->_saveCustomerEntity($entityRowsIn, $entityRowsUp)->_saveCustomerAttributes($attributes);
        }

        return $this;
    }

    /**
     * Save customer attributes.
     *
     * @return $this
     */
    protected function _saveCustomerAttributes(array $attributesData)
    {
        foreach ($attributesData as $tableName => $data) {
            $tableData = [];

            foreach ($data as $customerId => $attrData) {
                foreach ($attrData as $attributeId => $value) {
                    $tableData[] = [
                        'entity_id'      => $customerId,
                        'entity_type_id' => $this->_entityTypeId,
                        'attribute_id'   => $attributeId,
                        'value'          => $value,
                    ];
                }
            }

            $this->_connection->insertOnDuplicate($tableName, $tableData, ['value']);
        }

        return $this;
    }

    /**
     * Update and insert data in entity table.
     *
     * @param array $entityRowsIn Row for insert
     * @param array $entityRowsUp Row for update
     * @return $this
     */
    protected function _saveCustomerEntity(array $entityRowsIn, array $entityRowsUp)
    {
        if ($entityRowsIn) {
            $this->_connection->insertMultiple($this->_entityTable, $entityRowsIn);
        }

        if ($entityRowsUp) {
            $this->_connection->insertOnDuplicate(
                $this->_entityTable,
                $entityRowsUp,
                ['group_id', 'store_id', 'updated_at', 'created_at'],
            );
        }

        return $this;
    }

    /**
     * Get customer ID. Method tries to find ID from old and new customers. If it fails - it returns NULL.
     *
     * @param string $email
     * @param string $websiteCode
     * @return string|null
     */
    public function getCustomerId($email, $websiteCode)
    {
        if (isset($this->_oldCustomers[$email][$websiteCode])) {
            return $this->_oldCustomers[$email][$websiteCode];
        } elseif (isset($this->_newCustomers[$email][$websiteCode])) {
            return $this->_newCustomers[$email][$websiteCode];
        } else {
            return null;
        }
    }

    /**
     * EAV entity type code getter.
     *
     * @abstract
     * @return string
     */
    public function getEntityTypeCode()
    {
        return 'customer';
    }

    /**
     * Obtain scope of the row from row data.
     *
     * @return int
     */
    public function getRowScope(array $rowData)
    {
        $foundOptions = false;
        foreach (array_keys($this->_multiSelectAttributes) as $attrCode) {
            if ($rowData[$attrCode]) {
                $foundOptions = true;
            }
        }

        $scope = self::SCOPE_OPTIONS;
        if (strlen(trim($rowData[self::COL_EMAIL]))) {
            $scope = self::SCOPE_DEFAULT;
        } elseif ($foundOptions) {
            $scope = self::SCOPE_OPTIONS;
        } elseif (strlen(trim($rowData[self::COL_POSTCODE]))) {
            $scope = self::SCOPE_ADDRESS;
        }

        return $scope;
    }

    /**
     * Is attribute contains particular data (not plain entity attribute).
     *
     * @param string $attrCode
     * @return bool
     */
    public function isAttributeParticular($attrCode)
    {
        return parent::isAttributeParticular($attrCode) || $this->_addressEntity->isAttributeParticular($attrCode);
    }

    /**
     * Validate data row.
     *
     * @param int $rowNum
     * @return bool
     */
    public function validateRow(array $rowData, $rowNum)
    {
        static $email   = null; // e-mail is remembered through all customer rows
        static $website = null; // website is remembered through all customer rows

        if (isset($this->_validatedRows[$rowNum])) { // check that row is already validated
            return !isset($this->_invalidRows[$rowNum]);
        }

        $this->_validatedRows[$rowNum] = true;

        $rowScope = $this->getRowScope($rowData);

        if (self::SCOPE_DEFAULT == $rowScope) {
            $this->_processedEntitiesCount++;
        }

        $email        = $rowData[self::COL_EMAIL];
        $emailToLower = strtolower($rowData[self::COL_EMAIL]);
        $website      = $rowData[self::COL_WEBSITE];

        $oldCustomersToLower = array_change_key_case($this->_oldCustomers, CASE_LOWER);
        $newCustomersToLower = array_change_key_case($this->_newCustomers, CASE_LOWER);

        // BEHAVIOR_DELETE use specific validation logic
        if (Mage_ImportExport_Model_Import::BEHAVIOR_DELETE == $this->getBehavior()) {
            if (self::SCOPE_DEFAULT == $rowScope
                && !isset($oldCustomersToLower[$emailToLower][$website])
            ) {
                $this->addRowError(self::ERROR_EMAIL_SITE_NOT_FOUND, $rowNum);
            }
        } elseif (self::SCOPE_DEFAULT == $rowScope) { // row is SCOPE_DEFAULT = new customer block begins
            if (!Zend_Validate::is($email, 'EmailAddress')) {
                $this->addRowError(self::ERROR_INVALID_EMAIL, $rowNum);
            } elseif (!isset($this->_websiteCodeToId[$website])) {
                $this->addRowError(self::ERROR_INVALID_WEBSITE, $rowNum);
            } else {
                if (isset($newCustomersToLower[$emailToLower][$website])) {
                    $this->addRowError(self::ERROR_DUPLICATE_EMAIL_SITE, $rowNum);
                }

                $this->_newCustomers[$email][$website] = false;

                if (!empty($rowData[self::COL_STORE]) && !isset($this->_storeCodeToId[$rowData[self::COL_STORE]])) {
                    $this->addRowError(self::ERROR_INVALID_STORE, $rowNum);
                }

                // check password
                if (isset($rowData['password']) && strlen($rowData['password'])
                    && Mage::helper('core/string')->strlen($rowData['password']) < self::MAX_PASSWD_LENGTH
                ) {
                    $this->addRowError(self::ERROR_PASSWORD_LENGTH, $rowNum);
                }

                // check simple attributes
                foreach ($this->_attributes as $attrCode => $attrParams) {
                    if (in_array($attrCode, $this->_ignoredAttributes)) {
                        continue;
                    }

                    if (isset($rowData[$attrCode]) && strlen($rowData[$attrCode])) {
                        $this->isAttributeValid($attrCode, $attrParams, $rowData, $rowNum);
                    } elseif ($attrParams['is_required'] && !isset($oldCustomersToLower[$emailToLower][$website])) {
                        $this->addRowError(self::ERROR_VALUE_IS_REQUIRED, $rowNum, $attrCode);
                    }
                }
            }

            if (isset($this->_invalidRows[$rowNum])) {
                $email = false; // mark row as invalid for next address rows
            }
        } elseif (self::SCOPE_OPTIONS != $rowScope) {
            if ($email === null) { // first row is not SCOPE_DEFAULT
                $this->addRowError(self::ERROR_EMAIL_IS_EMPTY, $rowNum);
            } elseif ($email === false) { // SCOPE_DEFAULT row is invalid
                $this->addRowError(self::ERROR_ROW_IS_ORPHAN, $rowNum);
            }
        }

        if ($rowScope != self::SCOPE_OPTIONS) {
            $this->_addressEntity->validateRow($rowData, $rowNum);
        }

        return !isset($this->_invalidRows[$rowNum]);
    }
}
