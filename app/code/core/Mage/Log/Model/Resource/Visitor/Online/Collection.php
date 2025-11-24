<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Log
 */

/**
 * Log Online visitors collection
 *
 * @package    Mage_Log
 */
class Mage_Log_Model_Resource_Visitor_Online_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * joined fields array
     *
     * @var array
     */
    protected $_fields   = [];

    /**
     * Initialize collection model
     */
    protected function _construct()
    {
        $this->_init('log/visitor_online');
    }

    /**
     * Add Customer data to collection
     *
     * @return $this
     */
    public function addCustomerData()
    {
        $customer   = Mage::getModel('customer/customer');
        // alias => attribute_code
        $attributes = [
            'customer_lastname'   => 'lastname',
            'customer_middlename' => 'middlename',
            'customer_firstname'  => 'firstname',
            'customer_email'      => 'email',
        ];

        foreach ($attributes as $alias => $attributeCode) {
            $attribute = $customer->getAttribute($attributeCode);
            /** @var Mage_Eav_Model_Entity_Attribute_Abstract $attribute */

            if ($attribute->getBackendType() == 'static') {
                $tableAlias = 'customer_' . $attribute->getAttributeCode();

                $this->getSelect()->joinLeft(
                    [$tableAlias => $attribute->getBackend()->getTable()],
                    sprintf('%s.entity_id=main_table.customer_id', $tableAlias),
                    [$alias => $attribute->getAttributeCode()],
                );

                $this->_fields[$alias] = sprintf('%s.%s', $tableAlias, $attribute->getAttributeCode());
            } else {
                $tableAlias = 'customer_' . $attribute->getAttributeCode();

                $joinConds  = [
                    sprintf('%s.entity_id=main_table.customer_id', $tableAlias),
                    $this->getConnection()->quoteInto($tableAlias . '.attribute_id=?', $attribute->getAttributeId()),
                ];

                $this->getSelect()->joinLeft(
                    [$tableAlias => $attribute->getBackend()->getTable()],
                    implode(' AND ', $joinConds),
                    [$alias => 'value'],
                );

                $this->_fields[$alias] = sprintf('%s.value', $tableAlias);
            }
        }

        $this->setFlag('has_customer_data', true);
        return $this;
    }

    /**
     * Filter collection by specified website(s)
     *
     * @param array|int $websiteIds
     * @return $this
     */
    public function addWebsiteFilter($websiteIds)
    {
        if ($this->getFlag('has_customer_data')) {
            $this->getSelect()
                ->where('customer_email.website_id IN (?)', $websiteIds);
        }

        return $this;
    }

    /**
     * Add field filter to collection
     * If $attribute is an array will add OR condition with following format:
     * array(
     *     array('attribute'=>'firstname', 'like'=>'test%'),
     *     array('attribute'=>'lastname', 'like'=>'test%'),
     * )
     *
     * @param string $field
     * @param null|array|string $condition
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract
     * @see self::_getConditionSql for $condition
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if (isset($this->_fields[$field])) {
            $field = $this->_fields[$field];
        }

        return parent::addFieldToFilter($field, $condition);
    }
}
