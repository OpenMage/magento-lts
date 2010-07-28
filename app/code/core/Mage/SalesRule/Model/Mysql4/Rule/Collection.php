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
 * @package     Mage_SalesRule
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_SalesRule_Model_Mysql4_Rule_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('salesrule/rule');
    }

    public function setValidationFilter($websiteId, $customerGroupId, $couponCode='', $now=null)
    {
        if (is_null($now)) {
            $now = Mage::getModel('core/date')->date('Y-m-d');
        }

        $this->getSelect()->where('is_active=1');
        $this->getSelect()->where('find_in_set(?, website_ids)', (int)$websiteId);
        $this->getSelect()->where('find_in_set(?, customer_group_ids)', (int)$customerGroupId);

        if ($couponCode) {
            $this->getSelect()->joinLeft(
                array('extra_coupon' => $this->getTable('salesrule/coupon')),
                'extra_coupon.rule_id = main_table.rule_id AND extra_coupon.is_primary IS NULL',
                array()
            );
            $this->getSelect()->group('main_table.rule_id');
            $this->getSelect()->where(''
                . $this->getSelect()->getAdapter()->quoteInto(' main_table.coupon_type <> ?', Mage_SalesRule_Model_Rule::COUPON_TYPE_SPECIFIC)
                . $this->getSelect()->getAdapter()->quoteInto(' OR primary_coupon.code = ?', $couponCode)
            );
            $this->getSelect()->having(''
                . $this->getSelect()->getAdapter()->quoteInto(' main_table.coupon_type <> ?', Mage_SalesRule_Model_Rule::COUPON_TYPE_AUTO)
                . $this->getSelect()->getAdapter()->quoteInto(' OR FIND_IN_SET(?, GROUP_CONCAT(extra_coupon.code))', $couponCode)
            );
        } else {
            $this->getSelect()->where('main_table.coupon_type = ?', Mage_SalesRule_Model_Rule::COUPON_TYPE_NO_COUPON);
        }
        $this->getSelect()->where('from_date is null or from_date<=?', $now);
        $this->getSelect()->where('to_date is null or to_date>=?', $now);
        $this->getSelect()->order('sort_order');

        return $this;
    }

    /**
     * Filter collection by specified website IDs
     *
     * @param int|array $websiteIds
     * @return Mage_CatalogRule_Model_Mysql4_Rule_Collection
     */
    public function addWebsiteFilter($websiteIds)
    {
        if (!is_array($websiteIds)) {
            $websiteIds = array($websiteIds);
        }
        $parts = array();
        foreach ($websiteIds as $websiteId) {
            $parts[] = $this->getConnection()->quoteInto('FIND_IN_SET(?, main_table.website_ids)', $websiteId);
        }
        if ($parts) {
            $this->getSelect()->where(new Zend_Db_Expr(implode(' OR ', $parts)));
        }
        return $this;
    }

    /**
     * Init collection select
     *
     * @return unknown
     */
    public function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()
            ->joinLeft(
                array('primary_coupon' => $this->getTable('salesrule/coupon')),
                'main_table.rule_id = primary_coupon.rule_id AND primary_coupon.is_primary = 1',
                array('code')
            );
        return $this;
    }

    /**
     * Find product attribute in conditions or actions
     *
     * @param string $attributeCode
     * @return Mage_CatalogRule_Model_Mysql4_Rule_Collection
     */
    public function addAttributeInConditionFilter($attributeCode)
    {
        $match = sprintf('%%%s%%', substr(serialize(array('attribute' => $attributeCode)), 5, -1));
        $field = $this->_getMappedField('conditions_serialized');
        $cCond = $this->_getConditionSql($field, array('like' => $match));
        $field = $this->_getMappedField('actions_serialized');
        $aCond = $this->_getConditionSql($field, array('like' => $match));

        $this->getSelect()->where(sprintf('(%s OR %s)', $cCond, $aCond));

        return $this;
    }
}
