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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Customer EAV additional attribute resource collection
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Model_Entity_Attribute_Collection extends Mage_Eav_Model_Mysql4_Entity_Attribute_Collection
{
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
     * @var string
     */
    protected $_entityTypeCode      = 'customer';

    /**
     * Return customer entity type instance
     *
     * @return Mage_Eav_Model_Entity_Type
     */
    public function getEntityType()
    {
        if (is_null($this->_entityType)) {
            $this->_entityType = Mage::getSingleton('eav/config')->getEntityType($this->_entityTypeCode);
        }
        return $this->_entityType;
    }

    /**
     * Set Website scope
     *
     * @param Mage_Core_Model_Website|int $website
     * @return Mage_Customer_Model_Entity_Attribute_Collection
     */
    public function setWebsite($website)
    {
        $this->_website = Mage::app()->getWebsite($website);
        $this->addBindParam(':scope_website_id', $this->_website->getId());
        return $this;
    }

    /**
     * Return current website scope instance
     *
     * @return Mage_Core_Model_Website
     */
    public function getWebsite()
    {
        if (is_null($this->_website)) {
            $this->_website = Mage::app()->getStore()->getWebsite();
        }
        return $this->_website;
    }

    /**
     * Initialize collection select
     *
     * @return Mage_Customer_Model_Entity_Attribute_Collection
     */
    protected function _initSelect()
    {
        $entityType     = $this->getEntityType();
        $extraTable     = $entityType->getAdditionalAttributeTable();
        $mainDescribe   = $this->getConnection()->describeTable($this->getResource()->getMainTable());
        $mainColumns    = array();

        foreach (array_keys($mainDescribe) as $columnName) {
            $mainColumns[$columnName] = $columnName;
        }

        $this->getSelect()->from(array('main_table' => $this->getResource()->getMainTable()), $mainColumns);

        // additional attribute data table
        $extraDescribe  = $this->getConnection()->describeTable($this->getTable($extraTable));
        $extraColumns   = array();
        foreach (array_keys($extraDescribe) as $columnName) {
            if (isset($mainColumns[$columnName])) {
                continue;
            }
            $extraColumns[$columnName] = $columnName;
        }

        $this->getSelect()->join(
            array('additional_table' => $this->getTable($extraTable)),
            'additional_table.attribute_id = main_table.attribute_id',
            $extraColumns)
        ->where('main_table.entity_type_id = ?', $entityType->getId());

        // scope values

        $scopeDescribe  = $this->getConnection()->describeTable($this->getTable('customer/eav_attribute_website'));
        $scopeColumns   = array();
        foreach (array_keys($scopeDescribe) as $columnName) {
            if ($columnName == 'attribute_id') {
                continue;
            } else if ($columnName == 'website_id') {
                $scopeColumns['scope_website_id'] = $columnName;
            } else {
                if (isset($mainColumns[$columnName])) {
                    $alias = sprintf('scope_%s', $columnName);
                    $expression = new Zend_Db_Expr(sprintf('IFNULL(main_table.%s, scope_table.%s)',
                        $columnName, $columnName));
                    $this->addFilterToMap($columnName, $expression);
                    $scopeColumns[$alias] = $columnName;
                } else if (isset($extraColumns[$columnName])) {
                    $alias = sprintf('scope_%s', $columnName);
                    $expression = new Zend_Db_Expr(sprintf('IFNULL(additional_table.%s, scope_table.%s)',
                        $columnName, $columnName));
                    $this->addFilterToMap($columnName, $expression);
                    $scopeColumns[$alias] = $columnName;
                }
            }
        }

        $this->getSelect()->joinLeft(
            array('scope_table' => $this->getTable('customer/eav_attribute_website')),
            'scope_table.attribute_id = main_table.attribute_id AND scope_table.website_id = :scope_website_id',
            $scopeColumns
        );
        $this->addBindParam(':scope_website_id', $this->getWebsite()->getId());

        return $this;
    }

    /**
     * Specify attribute entity type filter
     * Entity type is defined
     *
     * @param   int $typeId
     * @return  Mage_Customer_Model_Entity_Attribute_Collection
     */
    public function setEntityTypeFilter($type)
    {
        return $this;
    }

    /**
     * Specify filter by "is_visible" field
     *
     * @return Mage_Customer_Model_Entity_Attribute_Collection
     */
    public function addVisibleFilter()
    {
        $this->addFieldToFilter('is_visible', 1);
        return $this;
    }

    /**
     * Exclude system hidden attributes
     *
     * @return Mage_Customer_Model_Entity_Attribute_Collection
     */
    public function addSystemHiddenFilter()
    {
        $field = '(CASE WHEN additional_table.is_system = 1 AND additional_table.is_visible = 0 THEN 1 ELSE 0 END)';
        $this->addFieldToFilter($field, 0);
        return $this;
    }

    /**
     * Add exclude hidden frontend input attribute filter to collection
     *
     * @return Mage_Customer_Model_Entity_Attribute_Collection
     */
    public function addExcludeHiddenFrontendFilter()
    {
        return $this->addFieldToFilter('main_table.frontend_input', array('neq' => 'hidden'));
    }
}
