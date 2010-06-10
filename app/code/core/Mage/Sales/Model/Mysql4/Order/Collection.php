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
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Flat sales order collection
 *
 */
class Mage_Sales_Model_Mysql4_Order_Collection extends Mage_Sales_Model_Mysql4_Collection_Abstract
{
    protected $_eventPrefix = 'sales_order_collection';
    protected $_eventObject = 'order_collection';

    protected function _construct()
    {
        $this->_init('sales/order');
    }

    /**
     * Add items count expr to collection select, backward capability with eav structure
     *
     * @return Mage_Sales_Model_Mysql4_Order_Collection
     */
    public function addItemCountExpr()
    {
        if (is_null($this->_fieldsToSelect)) { // If we select all fields from table,
                                               // we need to add column alias
            $this->getSelect()->columns(array('items_count'=>'total_item_count'));
        } else {
            $this->addFieldToSelect('total_item_count', 'items_count');
        }
        return $this;
    }

    /**
     * Minimize usual count select
     *
     * @return Varien_Db_Select
     */
    public function getSelectCountSql()
    {
        /* @var $countSelect Varien_Db_Select */
        $countSelect = parent::getSelectCountSql();

        $countSelect->resetJoinLeft();
        return $countSelect;
    }

    /**
     * Reset left join
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function _getAllIdsSelect($limit = null, $offset = null)
    {
        $idsSelect = parent::getAllIds($limit, $offset);
        $idsSelect->resetJoinLeft();
        return $idsSelect;
    }



    /**
     * Joins table sales_flat_order_address to select for billing and shipping orders addresses.
     * Creates corillation map
     *
     * @return Mage_Sales_Model_Mysql4_Collection_Abstract
     */
    protected function _addAddressFields()
    {
        $billingAliasName = 'billing_o_a';
        $shippingAliasName = 'shipping_o_a';
        $joinTable = $this->getTable('sales/order_address');

        $this->_map = array('fields' => array(
            'billing_firstname' => $billingAliasName . '.firstname',
            'billing_lastname' => $billingAliasName . '.lastname',
            'billing_telephone' => $billingAliasName . '.telephone',
            'billing_postcode' => $billingAliasName . '.postcode',

            'shipping_firstname' => $shippingAliasName . '.firstname',
            'shipping_lastname' => $shippingAliasName . '.lastname',
            'shipping_telephone' => $shippingAliasName . '.telephone',
            'shipping_postcode' => $shippingAliasName . '.postcode'
        ));

        $this
            ->getSelect()
            ->joinLeft(
                array($billingAliasName => $joinTable),
                "(main_table.entity_id = $billingAliasName.parent_id AND $billingAliasName.address_type = 'billing')",
                array(
                    $billingAliasName . '.firstname',
                    $billingAliasName . '.lastname',
                    $billingAliasName . '.telephone',
                    $billingAliasName . '.postcode'
                )
            )
            ->joinLeft(
                array($shippingAliasName => $joinTable),
                "(main_table.entity_id = $shippingAliasName.parent_id AND $shippingAliasName.address_type = 'shipping')",
                array(
                    $shippingAliasName . '.firstname',
                    $shippingAliasName . '.lastname',
                    $shippingAliasName . '.telephone',
                    $shippingAliasName . '.postcode'
                )
            );

        return $this;
    }

    /**
     * Specify collection select filter by attribute value
     *
     * @param array|string|Mage_Eav_Model_Entity_Attribute $attribute
     * @param array|integer|string|null $condition
     * @return Mage_Sales_Model_Mysql4_Collection_Abstract
     */
    public function addAttributeToFilter($attributes, $condition = null)
    {
        if (is_array($attributes)){
            if (!empty($attributes)){
                $this->_addAddressFields();

                foreach ($attributes as $attribute) {
                    parent::addAttributeToFilter($attribute['attribute'], $attribute);
                }
            }
        }
        else {
            return parent::addAttributeToFilter($attributes, $condition);
        }

        return $this;
    }

    /**
     * Add filter by specified billing agreements
     *
     * @param int|array $agreements
     * @return Mage_Sales_Model_Mysql4_Order_Collection
     */
    public function addBillingAgreementsFilter($agreements)
    {
        $agreements = (is_array($agreements)) ? $agreements : array($agreements);
        $this->getSelect()->joinInner(
            array('sbao' => $this->getTable('sales/billing_agreement_order')),
            'main_table.entity_id = sbao.order_id',
            array()
        )->where('sbao.agreement_id IN(?)', $agreements);
        return $this;
    }

    /**
     * Add filter by specified recurring profile id(s)
     *
     * @param array|int $ids
     * @return Mage_Sales_Model_Mysql4_Order_Collection
     */
    public function addRecurringProfilesFilter($ids)
    {
        $ids = (is_array($ids)) ? $ids : array($ids);
        $this->getSelect()->joinInner(
            array('srpo' => $this->getTable('sales/recurring_profile_order')),
            'main_table.entity_id = srpo.order_id',
            array()
        )->where('srpo.profile_id IN(?)', $ids);
        return $this;
    }
}
