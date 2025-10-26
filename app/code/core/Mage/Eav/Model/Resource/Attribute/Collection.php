<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Eav
 */

/**
 * EAV additional attribute resource collection (Using Forms)
 *
 * @package    Mage_Eav
 */
abstract class Mage_Eav_Model_Resource_Attribute_Collection extends Mage_Eav_Model_Resource_Entity_Attribute_Collection
{
    /**
     * code of password hash in customer's EAV tables
     */
    public const EAV_CODE_PASSWORD_HASH = 'password_hash';

    /**
     * Current website scope instance
     *
     * @var Mage_Core_Model_Website
     */
    protected $_website;

    /**
     * Attribute Entity Type Filter
     *
     * @var Mage_Eav_Model_Entity_Type
     */
    protected $_entityType;

    /**
     * Default attribute entity type code
     *
     * @return string
     */
    abstract protected function _getEntityTypeCode();

    /**
     * Get EAV website table
     *
     * Get table, where website-dependent attribute parameters are stored
     * If realization doesn't demand this functionality, let this function just return null
     *
     * @return string|null
     */
    abstract protected function _getEavWebsiteTable();

    /**
     * Return eav entity type instance
     *
     * @return Mage_Eav_Model_Entity_Type
     */
    public function getEntityType()
    {
        if ($this->_entityType === null) {
            $this->_entityType = Mage::getSingleton('eav/config')->getEntityType($this->_getEntityTypeCode());
        }

        return $this->_entityType;
    }

    /**
     * Set Website scope
     *
     * @param Mage_Core_Model_Website|int $website
     * @return $this
     */
    public function setWebsite($website)
    {
        $this->_website = Mage::app()->getWebsite($website);
        $this->addBindParam('scope_website_id', $this->_website->getId());
        return $this;
    }

    /**
     * Return current website scope instance
     *
     * @return Mage_Core_Model_Website
     */
    public function getWebsite()
    {
        if ($this->_website === null) {
            $this->_website = Mage::app()->getStore()->getWebsite();
        }

        return $this->_website;
    }

    /**
     * Initialize collection select
     *
     * @return $this
     */
    protected function _initSelect()
    {
        $select         = $this->getSelect();
        $connection     = $this->getConnection();
        $entityType     = $this->getEntityType();
        $extraTable     = $entityType->getAdditionalAttributeTable();
        $mainDescribe   = $this->getConnection()->describeTable($this->getResource()->getMainTable());
        $mainColumns    = [];

        foreach (array_keys($mainDescribe) as $columnName) {
            $mainColumns[$columnName] = $columnName;
        }

        $select->from(['main_table' => $this->getResource()->getMainTable()], $mainColumns);

        // additional attribute data table
        $extraDescribe  = $connection->describeTable($this->getTable($extraTable));
        $extraColumns   = [];
        foreach (array_keys($extraDescribe) as $columnName) {
            if (isset($mainColumns[$columnName])) {
                continue;
            }

            $extraColumns[$columnName] = $columnName;
        }

        $this->addBindParam('mt_entity_type_id', (int) $entityType->getId());
        $select
            ->join(
                ['additional_table' => $this->getTable($extraTable)],
                'additional_table.attribute_id = main_table.attribute_id',
                $extraColumns,
            )
            ->where('main_table.entity_type_id = :mt_entity_type_id');

        // scope values

        $scopeDescribe  = $connection->describeTable($this->_getEavWebsiteTable());
        unset($scopeDescribe['attribute_id']);
        $scopeColumns   = [];
        foreach (array_keys($scopeDescribe) as $columnName) {
            if ($columnName == 'website_id') {
                $scopeColumns['scope_website_id'] = $columnName;
            } elseif (isset($mainColumns[$columnName])) {
                $alias = sprintf('scope_%s', $columnName);
                $expression = $connection->getCheckSql(
                    'main_table.%s IS NULL',
                    'scope_table.%s',
                    'main_table.%s',
                );
                $expression = sprintf((string) $expression, $columnName, $columnName, $columnName);
                $this->addFilterToMap($columnName, $expression);
                $scopeColumns[$alias] = $columnName;
            } elseif (isset($extraColumns[$columnName])) {
                $alias = sprintf('scope_%s', $columnName);
                $expression = $connection->getCheckSql(
                    'additional_table.%s IS NULL',
                    'scope_table.%s',
                    'additional_table.%s',
                );
                $expression = sprintf((string) $expression, $columnName, $columnName, $columnName);
                $this->addFilterToMap($columnName, $expression);
                $scopeColumns[$alias] = $columnName;
            }
        }

        $select->joinLeft(
            ['scope_table' => $this->_getEavWebsiteTable()],
            'scope_table.attribute_id = main_table.attribute_id AND scope_table.website_id = :scope_website_id',
            $scopeColumns,
        );
        $websiteId = $this->getWebsite() ? (int) $this->getWebsite()->getId() : 0;
        $this->addBindParam('scope_website_id', $websiteId);

        return $this;
    }

    /**
     * Specify attribute entity type filter.
     * Entity type is defined.
     *
     * @param  int $type
     * @return $this
     */
    public function setEntityTypeFilter($type)
    {
        return $this;
    }

    /**
     * Specify filter by "is_visible" field
     *
     * @return $this
     */
    public function addVisibleFilter()
    {
        return $this->addFieldToFilter('is_visible', 1);
    }

    /**
     * Exclude system hidden attributes
     *
     * @return $this
     */
    public function addSystemHiddenFilter()
    {
        $field = '(CASE WHEN additional_table.is_system = 1 AND additional_table.is_visible = 0 THEN 1 ELSE 0 END)';
        $resultCondition = $this->_getConditionSql($field, 0);
        $this->_select->where($resultCondition);
        return $this;
    }

    /**
     * Exclude system hidden attributes but include password hash
     *
     * @return $this
     */
    public function addSystemHiddenFilterWithPasswordHash()
    {
        $field = '(CASE WHEN additional_table.is_system = 1 AND additional_table.is_visible = 0
            AND main_table.attribute_code != "' . self::EAV_CODE_PASSWORD_HASH . '" THEN 1 ELSE 0 END)';
        $resultCondition = $this->_getConditionSql($field, 0);
        $this->_select->where($resultCondition);
        return $this;
    }

    /**
     * Add exclude hidden frontend input attribute filter to collection
     *
     * @return $this
     */
    public function addExcludeHiddenFrontendFilter()
    {
        return $this->addFieldToFilter('main_table.frontend_input', ['neq' => 'hidden']);
    }
}
