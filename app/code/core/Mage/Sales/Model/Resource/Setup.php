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
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Setup Model of Sales Module
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Resource_Setup extends Mage_Eav_Model_Entity_Setup
{
    /**
     * List of entities converted from EAV to flat data structure
     *
     * @var $_flatEntityTables array
     */
    protected $_flatEntityTables     = array(
        'quote'             => 'sales/quote',
        'quote_item'        => 'sales/quote_item',
        'quote_address'     => 'sales/quote_address',
        'quote_address_item'=> 'sales/quote_address_item',
        'quote_address_rate'=> 'sales/quote_shipping_rate',
        'quote_payment'     => 'sales/quote_payment',
        'order'             => 'sales/order',
        'order_payment'     => 'sales/order_payment',
        'order_item'        => 'sales/order_item',
        'order_address'     => 'sales/order_address',
        'order_status_history' => 'sales/order_status_history',
        'invoice'           => 'sales/invoice',
        'invoice_item'      => 'sales/invoice_item',
        'invoice_comment'   => 'sales/invoice_comment',
        'creditmemo'        => 'sales/creditmemo',
        'creditmemo_item'   => 'sales/creditmemo_item',
        'creditmemo_comment'=> 'sales/creditmemo_comment',
        'shipment'          => 'sales/shipment',
        'shipment_item'     => 'sales/shipment_item',
        'shipment_track'    => 'sales/shipment_track',
        'shipment_comment'  => 'sales/shipment_comment',
    );

    /**
     * List of entities used with separate grid table
     *
     * @var $_flatEntitiesGrid array
     */
    protected $_flatEntitiesGrid     = array(
        'order',
        'invoice',
        'shipment',
        'creditmemo'
    );

    /**
     * Check if table exist for flat entity
     *
     * @param string $table
     * @return bool
     */
    protected function _flatTableExist($table)
    {
        $tablesList = $this->getConnection()->listTables();
        return in_array(strtoupper($this->getTable($table)), array_map('strtoupper', $tablesList));
    }

    /**
     * Add entity attribute. Overwrited for flat entities support
     *
     * @param int|string $entityTypeId
     * @param string $code
     * @param array $attr
     * @return Mage_Sales_Model_Resource_Setup
     */
    public function addAttribute($entityTypeId, $code, array $attr)
    {
        if (isset($this->_flatEntityTables[$entityTypeId]) &&
            $this->_flatTableExist($this->_flatEntityTables[$entityTypeId]))
        {
            $this->_addFlatAttribute($this->_flatEntityTables[$entityTypeId], $code, $attr);
            $this->_addGridAttribute($this->_flatEntityTables[$entityTypeId], $code, $attr, $entityTypeId);
        } else {
            parent::addAttribute($entityTypeId, $code, $attr);
        }
        return $this;
    }

    /**
     * Add attribute as separate column in the table
     *
     * @param string $table
     * @param string $attribute
     * @param array $attr
     * @return Mage_Sales_Model_Resource_Setup
     */
    protected function _addFlatAttribute($table, $attribute, $attr)
    {
        $tableInfo = $this->getConnection()->describeTable($this->getTable($table));
        if (isset($tableInfo[$attribute])) {
            return $this;
        }
        $columnDefinition = $this->_getAttributeColumnDefinition($attribute, $attr);
        $this->getConnection()->addColumn($this->getTable($table), $attribute, $columnDefinition);
        return $this;
    }

    /**
     * Add attribute to grid table if necessary
     *
     * @param string $table
     * @param string $attribute
     * @param array $attr
     * @param string $entityTypeId
     * @return Mage_Sales_Model_Resource_Setup
     */
    protected function _addGridAttribute($table, $attribute, $attr, $entityTypeId)
    {
        if (in_array($entityTypeId, $this->_flatEntitiesGrid) && !empty($attr['grid'])) {
            $columnDefinition = $this->_getAttributeColumnDefinition($attribute, $attr);
            $this->getConnection()->addColumn($this->getTable($table . '_grid'), $attribute, $columnDefinition);
        }
        return $this;
    }

    /**
     * Retrieve definition of column for create in flat table
     *
     * @param string $code
     * @param array $data
     * @return array
     */
    protected function _getAttributeColumnDefinition($code, $data)
    {
        // Convert attribute type to column info
        $data['type'] = isset($data['type']) ? $data['type'] : 'varchar';
        $type = null;
        $length = null;
        switch ($data['type']) {
            case 'timestamp':
                $type = Varien_Db_Ddl_Table::TYPE_TIMESTAMP;
                break;
            case 'datetime':
                $type = Varien_Db_Ddl_Table::TYPE_DATETIME;
                break;
            case 'decimal':
                $type = Varien_Db_Ddl_Table::TYPE_DECIMAL;
                $length = '12,4';
                break;
            case 'int':
                $type = Varien_Db_Ddl_Table::TYPE_INTEGER;
                break;
            case 'text':
                $type = Varien_Db_Ddl_Table::TYPE_TEXT;
                $length = 65536;
                break;
            case 'char':
            case 'varchar':
                $type = Varien_Db_Ddl_Table::TYPE_TEXT;
                $length = 255;
                break;
        }
        if ($type !== null) {
            $data['type'] = $type;
            $data['length'] = $length;
        }

        $data['nullable'] = isset($data['required']) ? !$data['required'] : true;
        $data['comment']  = isset($data['comment']) ? $data['comment'] : ucwords(str_replace('_', ' ', $code));
        return $data;
    }

    /**
     * Retrieve default entities
     *
     * @return array
     */
    public function getDefaultEntities()
    {
        return array(
            'quote'=>array(
                'entity_model'  => 'sales/quote',
                'table'         => 'sales/quote',
                'attributes' => array(
                    'entity_id'             => array('type'=>'static'),
                    'is_active'             => array('type'=>'static'),
                    'store_id'              => array('type'=>'static'),
                    'remote_ip'             => array('type'=>'static'),
                    'checkout_method'       => array('type'=>'static'),
                    'password_hash'         => array('type'=>'static'),
                    'orig_order_id'         => array('type'=>'static'),
                    'converted_at'          => array('type'=>'static'),
                    'reserved_order_id'     => array('type'=>'static'),

                    'coupon_code'           => array('type'=>'static'),
                    'global_currency_code'  => array('type'=>'static'),
                    'base_currency_code'    => array('type'=>'static'),
                    'store_currency_code'   => array('type'=>'static'),
                    'quote_currency_code'   => array('type'=>'static'),
                    'store_to_base_rate'    => array('type'=>'static'),
                    'store_to_quote_rate'   => array('type'=>'static'),
                    'base_to_global_rate'   => array('type'=>'static'),
                    'base_to_quote_rate'    => array('type'=>'static'),

                    'items_count'           => array('type'=>'static'),
                    'items_qty'             => array('type'=>'static'),

                    'grand_total'           => array('type'=>'static'),
                    'base_grand_total'      => array('type'=>'static'),

                    'applied_rule_ids'      => array('type'=>'static'),

                    'is_virtual'            => array('type'=>'static'),
                    'is_multi_shipping'     => array('type'=>'static'),

                    'customer_id'           => array('type'=>'static'),
                    'customer_tax_class_id' => array('type'=>'static'),
                    'customer_group_id'     => array('type'=>'static'),
                    'customer_email'        => array('type'=>'static'),
                    'customer_prefix'       => array('type'=>'static'),
                    'customer_firstname'    => array('type'=>'static'),
                    'customer_middlename'   => array('type'=>'static'),
                    'customer_lastname'     => array('type'=>'static'),
                    'customer_suffix'       => array('type'=>'static'),
                    'customer_note'         => array('type'=>'static'),
                    'customer_note_notify'  => array('type'=>'static'),
                    'customer_is_guest'     => array('type'=>'static'),
                    'customer_taxvat'       => array('type'=>'static'),
                    'customer_dob'          => array('type'=>'static'),
                    'customer_gender'       => array('type'=>'static'),
                ),
            ),

            'quote_item' => array(
                'entity_model'  => 'sales/quote_item',
                'table'         => 'sales/quote_item',
                'attributes'    => array(
                    'product_id'                => array('type'=>'static'),
                    'super_product_id'          => array('type'=>'static'),
                    'parent_product_id'         => array('type'=>'static'),
                    'sku'                       => array('type'=>'static'),
                    'name'                      => array('type'=>'static'),
                    'description'               => array('type'=>'static'),

                    'weight'                    => array('type'=>'static'),
                    'free_shipping'             => array('type'=>'static'),
                    'qty'                       => array('type'=>'static'),
                    'is_qty_decimal'            => array('type'=>'static'),

                    'price'                     => array('type'=>'static'),
                    'custom_price'              => array('type'=>'static'),
                    'discount_percent'          => array('type'=>'static'),
                    'discount_amount'           => array('type'=>'static'),
                    'no_discount'               => array('type'=>'static'),
                    'tax_percent'               => array('type'=>'static'),
                    'tax_amount'                => array('type'=>'static'),
                    'row_total'                 => array('type'=>'static'),
                    'row_total_with_discount'   => array('type'=>'static'),

                    'base_price'                => array('type'=>'static'),
                    'base_discount_amount'      => array('type'=>'static'),
                    'base_tax_amount'           => array('type'=>'static'),
                    'base_row_total'            => array('type'=>'static'),

                    'row_weight'                => array('type'=>'static'),
                    'applied_rule_ids'          => array('type'=>'static'),
                    'additional_data'           => array('type'=>'static'),
                ),
            ),

            'quote_address' => array(
                'entity_model'  => 'sales/quote_address',
                'table'         => 'sales/quote_address',
                'attributes'    => array(
                    'address_type'              => array('type'=>'static'),

                    'customer_id'               => array('type'=>'static'),
                    'customer_address_id'       => array('type'=>'static'),
                    'save_in_address_book'      => array('type'=>'static'),
                    'email'                     => array('type'=>'static'),
                    'prefix'                    => array('type'=>'static'),
                    'firstname'                 => array('type'=>'static'),
                    'middlename'                => array('type'=>'static'),
                    'lastname'                  => array('type'=>'static'),
                    'suffix'                    => array('type'=>'static'),
                    'company'                   => array('type'=>'static'),
                    'street'                    => array('type'=>'static'),
                    'city'                      => array('type'=>'static'),
                    'region'                    => array('type'=>'static'),
                    'region_id'                 => array('type'=>'static'),
                    'postcode'                  => array('type'=>'static'),
                    'country_id'                => array('type'=>'static'),
                    'telephone'                 => array('type'=>'static'),
                    'fax'                       => array('type'=>'static'),

                    'same_as_billing'           => array('type'=>'static'),
                    'free_shipping'             => array('type'=>'static'),
                    'weight'                    => array('type'=>'static'),
                    'collect_shipping_rates'    => array('type'=>'static'),

                    'shipping_method'           => array('type'=>'static'),
                    'shipping_description'      => array('type'=>'static'),

                    'subtotal'                  => array('type'=>'static'),
                    'subtotal_with_discount'    => array('type'=>'static'),
                    'tax_amount'                => array('type'=>'static'),
                    'shipping_amount'           => array('type'=>'static'),
                    'shipping_tax_amount'       => array('type'=>'static'),
                    'discount_amount'           => array('type'=>'static'),
                    'grand_total'               => array('type'=>'static'),

                    'base_subtotal'             => array('type'=>'static'),
                    'base_subtotal_with_discount' => array('type'=>'static'),
                    'base_tax_amount'           => array('type'=>'static'),
                    'base_shipping_amount'      => array('type'=>'static'),
                    'base_shipping_tax_amount'  => array('type'=>'static'),
                    'base_discount_amount'      => array('type'=>'static'),
                    'base_grand_total'          => array('type'=>'static'),

                    'customer_notes'            => array('type'=>'static'),
                    'applied_taxes'             => array('type'=>'text'),
                ),
            ),
            'quote_address_item' => array(
                'entity_model'  => 'sales/quote_address_item',
                'table'         =>'sales/quote_entity',
                'attributes'            => array(
                    'quote_item_id'             => array('type'=>'int'),
                    'product_id'                => array('type'=>'int'),
                    'super_product_id'          => array('type'=>'int'),
                    'parent_product_id'         => array('type'=>'int'),
                    'sku'                       => array(),
                    'image'                     => array(),
                    'name'                      => array(),
                    'description'               => array('type'=>'text'),

                    'weight'                    => array('type'=>'decimal'),
                    'free_shipping'             => array('type'=>'int'),
                    'qty'                       => array('type'=>'decimal'),
                    'is_qty_decimal'            => array('type'=>'int'),

                    'price'                     => array('type'=>'decimal'),
                    'discount_percent'          => array('type'=>'decimal'),
                    'discount_amount'           => array('type'=>'decimal'),
                    'no_discount'               => array('type'=>'int'),
                    'tax_percent'               => array('type'=>'decimal'),
                    'tax_amount'                => array('type'=>'decimal'),
                    'row_total'                 => array('type'=>'decimal'),
                    'row_total_with_discount'   => array('type'=>'decimal'),

                    'base_price'                => array('type'=>'decimal'),
                    'base_discount_amount'      => array('type'=>'decimal'),
                    'base_tax_amount'           => array('type'=>'decimal'),
                    'base_row_total'            => array('type'=>'decimal'),

                    'row_weight'                => array('type'=>'decimal'),
                    'applied_rule_ids'          => array(),
                    'additional_data'           => array('type'=>'text'),
                ),
            ),
            'quote_address_rate' => array(
                'entity_model'      => 'sales/quote_address_rate',
                'table'             => 'sales/quote_entity',
                'attributes'        => array(
                    'code'                  => array(),
                    'carrier'               => array(),
                    'carrier_title'         => array(),
                    'method'                => array(),
                    'method_description'    => array('type'=>'text'),
                    'price'                 => array('type'=>'decimal'),
                    'error_message'         => array('type'=>'text'),
                ),
            ),
            'quote_payment' => array(
                'entity_model'  => 'sales/quote_payment',
                'table'         =>'sales/quote_entity',
                'attributes'    => array(
                    'method'            => array(),
                    'additional_data'   => array('type'=>'text'),
                    'po_number'         => array(),
                    'cc_type'           => array(),
                    'cc_number_enc'     => array(),
                    'cc_last4'          => array(),
                    'cc_owner'          => array(),
                    'cc_exp_month'      => array('type'=>'int'),
                    'cc_exp_year'       => array('type'=>'int'),
                    'cc_cid_enc'        => array(),
                    'cc_ss_issue'       => array(),
                    'cc_ss_start_month' => array('type'=>'int'),
                    'cc_ss_start_year'  => array('type'=>'int'),
                ),
            ),

            'order' => array(
                'entity_model'          => 'sales/order',
                'table'                 => 'sales/order',
                'increment_model'       => 'eav/entity_increment_numeric',
                'increment_per_store'   =>true,
                'backend_prefix'        =>'sales_entity/order_attribute_backend',
                'attributes'            => array(
                    'entity_id'             => array(
                        'type'      =>'static',
                        'backend'   =>'sales_entity/order_attribute_backend_parent'
                    ),
                    'store_id'                  => array('type'=>'static'),
                    'store_name'                => array('type'=>'varchar'),
                    'remote_ip'                 => array(),

                    'status'                    => array('type'=>'varchar'),
                    'state'                     => array('type'=>'varchar'),
                    'hold_before_status'        => array('type'=>'varchar'),
                    'hold_before_state'         => array('type'=>'varchar'),

                    'relation_parent_id'        => array('type'=>'varchar'),
                    'relation_parent_real_id'   => array('type'=>'varchar'),
                    'relation_child_id'         => array('type'=>'varchar'),
                    'relation_child_real_id'    => array('type'=>'varchar'),
                    'original_increment_id'     => array('type'=>'varchar'),
                    'edit_increment'            => array('type'=>'int'),

                    'ext_order_id'              => array('type'=>'varchar'),
                    'ext_customer_id'           => array('type'=>'varchar'),

                    'quote_id'                  => array('type'=>'int'),
                    'quote_address_id'          => array('type'=>'int'),
                    'billing_address_id'        => array('type'=>'int', 'backend'=>'_billing'),
                    'shipping_address_id'       => array('type'=>'int', 'backend'=>'_shipping'),

                    'coupon_code'               => array(),
                    'applied_rule_ids'          => array(),

                    'global_currency_code'      => array(),
                    'base_currency_code'        => array(),
                    'store_currency_code'       => array(),
                    'order_currency_code'       => array(),
                    'store_to_base_rate'        => array('type'=>'decimal'),
                    'store_to_order_rate'       => array('type'=>'decimal'),
                    'base_to_global_rate'       => array('type'=>'decimal'),
                    'base_to_order_rate'        => array('type'=>'decimal'),

                    'is_virtual'                => array('type'=>'int'),

                    'shipping_method'           => array(),
                    'shipping_description'      => array(),
                    'weight'                    => array('type'=>'decimal'),

                    'tax_amount'                => array('type'=>'static'),
                    'shipping_amount'           => array('type'=>'static'),
                    'shipping_tax_amount'       => array('type'=>'static'),
                    'discount_amount'           => array('type'=>'static'),

                    'subtotal'                  => array('type'=>'static'),
                    'grand_total'               => array('type'=>'static'),
                    'total_paid'                => array('type'=>'static'),
                    'total_due'                 => array('type'=>'decimal'),
                    'total_refunded'            => array('type'=>'static'),
                    'total_qty_ordered'         => array('type'=>'static'),
                    'total_canceled'            => array('type'=>'static'),
                    'total_invoiced'            => array('type'=>'static'),
                    'total_online_refunded'     => array('type'=>'static'),
                    'total_offline_refunded'    => array('type'=>'static'),
                    'adjustment_positive'       => array('type'=>'decimal'),
                    'adjustment_negative'       => array('type'=>'decimal'),

                    'base_tax_amount'           => array('type'=>'static'),
                    'base_shipping_amount'      => array('type'=>'static'),
                    'base_shipping_tax_amount'  => array('type'=>'static'),
                    'base_discount_amount'      => array('type'=>'static'),

                    'base_subtotal'             => array('type'=>'static'),
                    'base_grand_total'          => array('type'=>'static'),
                    'base_total_paid'           => array('type'=>'static'),
                    'base_total_due'            => array('type'=>'decimal'),
                    'base_total_refunded'       => array('type'=>'static'),
                    'base_total_qty_ordered'    => array('type'=>'static'),
                    'base_total_canceled'       => array('type'=>'static'),
                    'base_total_invoiced'       => array('type'=>'static'),
                    'base_total_online_refunded' => array('type'=>'static'),
                    'base_total_offline_refunded'=> array('type'=>'static'),
                    'base_adjustment_positive'  => array('type'=>'decimal'),
                    'base_adjustment_negative'  => array('type'=>'decimal'),

                    'subtotal_refunded'         => array('type'=>'static'),
                    'subtotal_canceled'         => array('type'=>'static'),
                    'discount_refunded'         => array('type'=>'static'),
                    'discount_canceled'         => array('type'=>'static'),
                    'discount_invoiced'         => array('type'=>'static'),
                    'subtotal_invoiced'         => array('type'=>'static'),
                    'tax_refunded'              => array('type'=>'static'),
                    'tax_canceled'              => array('type'=>'static'),
                    'tax_invoiced'              => array('type'=>'static'),
                    'shipping_refunded'         => array('type'=>'static'),
                    'shipping_canceled'         => array('type'=>'static'),
                    'shipping_invoiced'         => array('type'=>'static'),
                    'base_subtotal_refunded'    => array('type'=>'static'),
                    'base_subtotal_canceled'    => array('type'=>'static'),
                    'base_discount_refunded'    => array('type'=>'static'),
                    'base_discount_canceled'    => array('type'=>'static'),
                    'base_discount_invoiced'    => array('type'=>'static'),
                    'base_subtotal_invoiced'    => array('type'=>'static'),
                    'base_tax_refunded'         => array('type'=>'static'),
                    'base_tax_canceled'         => array('type'=>'static'),
                    'base_tax_invoiced'         => array('type'=>'static'),
                    'base_shipping_refunded'    => array('type'=>'static'),
                    'base_shipping_canceled'    => array('type'=>'static'),
                    'base_shipping_invoiced'    => array('type'=>'static'),

                    'protect_code'              => array('type' => 'static'),

                    'customer_id'               => array('type'=>'static', 'visible'=>false),
                    'customer_group_id'         => array('type'=>'int', 'visible'=>false),
                    'customer_email'            => array('type'=>'varchar', 'visible'=>false),
                    'customer_prefix'           => array('type'=>'varchar', 'visible'=>false),
                    'customer_firstname'        => array('type'=>'varchar', 'visible'=>false),
                    'customer_middlename'       => array('type'=>'varchar', 'visible'=>false),
                    'customer_lastname'         => array('type'=>'varchar', 'visible'=>false),
                    'customer_suffix'           => array('type'=>'varchar', 'visible'=>false),
                    'customer_note'             => array('type'=>'text', 'visible'=>false),
                    'customer_note_notify'      => array('type'=>'int', 'visible'=>false),
                    'customer_is_guest'         => array('type'=>'int', 'visible'=>false),
                    'email_sent'                => array('type'=>'int', 'visible'=>false),
                    'customer_taxvat'           => array('type'=>'varchar', 'visible'=>false),
                    'customer_dob'              => array('type'=>'datetime',
                        'backend' => 'eav/entity_attribute_backend_datetime'),
                    'customer_gender'           => array('type'=>'int', 'visible'=>false),
                ),
            ),
            'order_address'     => array(
                'entity_model'      => 'sales/order_address',
                'table'             =>'sales/order_entity',
                'attributes'         => array(
                    'parent_id'             => array('type'=>'static', 'backend'=>'sales_entity/order_attribute_backend_child'),
                    'quote_address_id'      => array('type'=>'int'),
                    'address_type'          => array(),
                    'customer_id'           => array('type'=>'int'),
                    'customer_address_id'   => array('type'=>'int'),
                    'email'                 => array(),
                    'prefix'                => array(),
                    'firstname'             => array(),
                    'middlename'            => array(),
                    'lastname'              => array(),
                    'suffix'                => array(),
                    'company'               => array(),
                    'street'                => array(),
                    'city'                  => array(),
                    'region'                => array(),
                    'region_id'             => array('type'=>'int'),
                    'postcode'              => array(),
                    'country_id'            => array('type'=>'varchar'),
                    'telephone'             => array(),
                    'fax'                   => array(),

                ),
            ),
            'order_item'        => array(
                'entity_model'      => 'sales/order_item',
                'table'             =>'sales/order_entity',
                'attributes'        => array(
                    'parent_id'                 => array(
                        'type'      =>'static',
                        'backend'   =>'sales_entity/order_attribute_backend_child'
                    ),

                    'quote_item_id'             => array('type'=>'int'),
                    'product_id'                => array('type'=>'int'),
                    'super_product_id'          => array('type'=>'int'),
                    'parent_product_id'         => array('type'=>'int'),
                    'is_virtual'                => array('type'=>'int'),
                    'sku'                       => array(),
                    'name'                      => array(),
                    'description'               => array('type'=>'text'),
                    'weight'                    => array('type'=>'decimal'),

                    'is_qty_decimal'            => array('type'=>'int'),
                    'qty_ordered'               => array('type'=>'decimal'),
                    'qty_backordered'           => array('type'=>'decimal'),
                    'qty_invoiced'              => array('type'=>'decimal'),
                    'qty_canceled'              => array('type'=>'decimal'),
                    'qty_shipped'               => array('type'=>'decimal'),
                    'qty_refunded'              => array('type'=>'decimal'),

                    'original_price'            => array('type'=>'decimal'),
                    'price'                     => array('type'=>'decimal'),
                    'cost'                      => array('type'=>'decimal'),

                    'discount_percent'          => array('type'=>'decimal'),
                    'discount_amount'           => array('type'=>'decimal'),
                    'discount_invoiced'         => array('type'=>'decimal'),

                    'tax_percent'               => array('type'=>'decimal'),
                    'tax_amount'                => array('type'=>'decimal'),
                    'tax_invoiced'              => array('type'=>'decimal'),

                    'row_total'                 => array('type'=>'decimal'),
                    'row_weight'                => array('type'=>'decimal'),
                    'row_invoiced'              => array('type'=>'decimal'),
                    'invoiced_total'            => array('type'=>'decimal'),
                    'amount_refunded'           => array('type'=>'decimal'),

                    'base_price'                => array('type'=>'decimal'),
                    'base_original_price'       => array('type'=>'decimal'),
                    'base_discount_amount'      => array('type'=>'decimal'),
                    'base_discount_invoiced'    => array('type'=>'decimal'),
                    'base_tax_amount'           => array('type'=>'decimal'),
                    'base_tax_invoiced'         => array('type'=>'decimal'),
                    'base_row_total'            => array('type'=>'decimal'),
                    'base_row_invoiced'         => array('type'=>'decimal'),
                    'base_invoiced_total'       => array('type'=>'decimal'),
                    'base_amount_refunded'      => array('type'=>'decimal'),

                    'applied_rule_ids'          => array(),
                    'additional_data'           => array('type'=>'text'),
                ),
            ),
            'order_payment' => array(
                'entity_model'      => 'sales/order_payment',
                'table'=>'sales/order_entity',
                'attributes' => array(
                    'parent_id' => array(
                        'type'=>'static',
                        'backend'=>'sales_entity/order_attribute_backend_child'
                    ),
                    'quote_payment_id'      => array('type'=>'int'),
                    'method'                => array(),
                    'additional_data'       => array('type'=>'text'),
                    'last_trans_id'         => array(),
                    'po_number'     => array(),

                    'cc_type'       => array(),
                    'cc_number_enc' => array(),
                    'cc_last4'      => array(),
                    'cc_owner'      => array(),
                    'cc_exp_month'  => array(),
                    'cc_exp_year'   => array(),

                    'cc_ss_issue' => array(),
                    'cc_ss_start_month' => array(),
                    'cc_ss_start_year' => array(),

                    'cc_status'             => array(),
                    'cc_status_description' => array(),
                    'cc_trans_id'           => array(),
                    'cc_approval'           => array(),
                    'cc_avs_status'         => array(),
                    'cc_cid_status'         => array(),

                    'cc_debug_request_body' => array(),
                    'cc_debug_response_body'=> array(),
                    'cc_debug_response_serialized' => array(),

                    'anet_trans_method'     => array(),
                    'echeck_routing_number' => array(),
                    'echeck_bank_name'      => array(),
                    'echeck_account_type'   => array(),
                    'echeck_account_name'   => array(),
                    'echeck_type'           => array(),

                    'amount_ordered'    => array('type'=>'decimal'),
                    'amount_authorized' => array('type'=>'decimal'),
                    'amount_paid'       => array('type'=>'decimal'),
                    'amount_canceled'   => array('type'=>'decimal'),
                    'amount_refunded'   => array('type'=>'decimal'),
                    'shipping_amount'   => array('type'=>'decimal'),
                    'shipping_captured' => array('type'=>'decimal'),
                    'shipping_refunded' => array('type'=>'decimal'),

                    'base_amount_ordered'    => array('type'=>'decimal'),
                    'base_amount_authorized' => array('type'=>'decimal'),
                    'base_amount_paid'       => array('type'=>'decimal'),
                    'base_amount_paid_online' => array('type'=>'decimal'),
                    'base_amount_canceled'   => array('type'=>'decimal'),
                    'base_amount_refunded'   => array('type'=>'decimal'),
                    'base_amount_refunded_online' => array('type'=>'decimal'),
                    'base_shipping_amount'   => array('type'=>'decimal'),
                    'base_shipping_captured' => array('type'=>'decimal'),
                    'base_shipping_refunded' => array('type'=>'decimal'),
                ),
            ),

            'order_status_history' => array(
                'entity_model'      => 'sales/order_status_history',
                'table'=>'sales/order_entity',
                'attributes' => array(
                    'parent_id' => array(
                        'type'=>'static',
                        'backend'=>'sales_entity/order_attribute_backend_child'
                    ),
                    'status'    => array('type'=>'varchar'),
                    'comment'   => array('type'=>'text'),
                    'is_customer_notified' => array('type'=>'int'),
                ),
            ),

            'invoice' => array(
                'entity_model'      => 'sales/order_invoice',
                'table'             =>'sales/order_entity',
                'increment_model'   =>'eav/entity_increment_numeric',
                'increment_per_store'=>true,
                'backend_prefix'    =>'sales_entity/order_attribute_backend',
                'attributes' => array(
                    'entity_id' => array(
                        'type'=>'static',
                        'backend'=>'sales_entity/order_invoice_attribute_backend_parent'
                    ),

                    'state'    => array('type'=>'int'),
                    'is_used_for_refund' => array('type'=>'int'),
                    'transaction_id' => array(),


                    'order_id'              => array(
                        'type'=>'int',
                        'backend'=>'sales_entity/order_invoice_attribute_backend_order'
                    ),

                    'billing_address_id'    => array('type'=>'int'),
                    'shipping_address_id'   => array('type'=>'int'),

                    'global_currency_code'    => array(),
                    'base_currency_code'    => array(),
                    'store_currency_code'   => array(),
                    'order_currency_code'   => array(),
                    'store_to_base_rate'    => array('type'=>'decimal'),
                    'store_to_order_rate'   => array('type'=>'decimal'),
                    'base_to_global_rate'    => array('type'=>'decimal'),
                    'base_to_order_rate'   => array('type'=>'decimal'),

                    'subtotal'          => array('type'=>'decimal'),
                    'discount_amount'   => array('type'=>'decimal'),
                    'tax_amount'        => array('type'=>'decimal'),
                    'shipping_amount'   => array('type'=>'decimal'),
                    'grand_total'       => array('type'=>'decimal'),
                    'total_qty'         => array('type'=>'decimal'),

                    'can_void_flag'     => array('type'=>'int'),

                    'base_subtotal'          => array('type'=>'decimal'),
                    'base_discount_amount'   => array('type'=>'decimal'),
                    'base_tax_amount'        => array('type'=>'decimal'),
                    'base_shipping_amount'   => array('type'=>'decimal'),
                    'base_grand_total'       => array('type'=>'decimal'),
                    'email_sent' => array('type'=>'int'),
                    'store_id'   => array('type'=>'static'),
                ),
            ),

            'invoice_item' => array(
                'entity_model'      => 'sales/order_invoice_item',
                //'table'=>'sales/invoice',
                'table'=>'sales/order_entity',
                'attributes' => array(
                    'parent_id'     => array(
                        'type'=>'static',
                        'backend'=>'sales_entity/order_invoice_attribute_backend_child'
                    ),
                    'order_item_id' => array('type'=>'int'),
                    'product_id'    => array('type'=>'int'),
                    'name'          => array(),
                    'description'   => array('type'=>'text'),
                    'sku'           => array(),
                    'qty'           => array('type'=>'decimal'),
                    'cost'          => array('type'=>'decimal'),
                    'price'         => array('type'=>'decimal'),
                    'discount_amount' => array('type'=>'decimal'),
                    'tax_amount'    => array('type'=>'decimal'),
                    'row_total'     => array('type'=>'decimal'),

                    'base_price'         => array('type'=>'decimal'),
                    'base_discount_amount' => array('type'=>'decimal'),
                    'base_tax_amount'    => array('type'=>'decimal'),
                    'base_row_total'     => array('type'=>'decimal'),

                    'additional_data'   => array('type'=>'text'),
                ),
            ),

            'invoice_comment' => array(
                'entity_model'      => 'sales/order_invoice_comment',
                'table'=>'sales/order_entity',
                'attributes' => array(
                    'parent_id' => array(
                        'type'=>'static',
                        'backend'=>'sales_entity/order_invoice_attribute_backend_child'
                    ),
                    'comment' => array('type'=>'text'),
                    'is_customer_notified' => array('type'=>'int'),
                ),
            ),



            'shipment' => array(
                'entity_model'      => 'sales/order_shipment',
                //'table'=>'sales/shipment',
                'table'=>'sales/order_entity',
                'increment_model'=>'eav/entity_increment_numeric',
                'increment_per_store'=>true,
                'backend_prefix'=>'sales_entity/order_attribute_backend',
                'attributes' => array(
                    'entity_id'     => array(
                        'type'=>'static',
                        'backend'=>'sales_entity/order_shipment_attribute_backend_parent'
                    ),

                    'customer_id'   => array('type'=>'int'),
                    'order_id'      => array('type'=>'int'),
                    'shipment_status'     => array('type'=>'int'),
                    'billing_address_id'    => array('type'=>'int'),
                    'shipping_address_id'   => array('type'=>'int'),

                    'total_qty'         => array('type'=>'decimal'),
                    'total_weight'      => array('type'=>'decimal'),
                    'email_sent'        => array('type'=>'int'),
                    'store_id'          => array('type' => 'static'),
                ),
            ),

            'shipment_item' => array(
                'entity_model'      => 'sales/order_shipment_item',
                //'table'=>'sales/shipment',
                'table'=>'sales/order_entity',
                'attributes' => array(
                    'parent_id'     => array(
                        'type'=>'static',
                        'backend'=>'sales_entity/order_shipment_attribute_backend_child'
                    ),
                    'order_item_id' => array('type'=>'int'),
                    'product_id'    => array('type'=>'int'),
                    'name'          => array(),
                    'description'   => array('type'=>'text'),
                    'sku'           => array(),
                    'qty'           => array('type'=>'decimal'),
                    'price'         => array('type'=>'decimal'),
                    'weight'        => array('type'=>'decimal'),
                    'row_total'     => array('type'=>'decimal'),

                    'additional_data'   => array('type'=>'text'),
                ),
            ),

            'shipment_comment' => array(
                'entity_model'      => 'sales/order_shipment_comment',
                'table'=>'sales/order_entity',
                'attributes' => array(
                    'parent_id' => array(
                        'type'=>'static',
                        'backend'=>'sales_entity/order_shipment_attribute_backend_child'
                    ),
                    'comment' => array('type'=>'text'),
                    'is_customer_notified' => array('type'=>'int'),
                ),
            ),

            'shipment_track' => array(
                'entity_model'      => 'sales/order_shipment_track',
                'table'=>'sales/order_entity',
                'attributes' => array(
                    'parent_id'     => array(
                        'type'=>'static',
                        'backend'=>'sales_entity/order_shipment_attribute_backend_child'
                    ),
                    'order_id'      => array('type'=>'int'),
                    'number'        => array('type'=>'text'),
                    'carrier_code'  => array('type'=>'varchar'),
                    'title'         => array('type'=>'varchar'),
                    'description'   => array('type'=>'text'),
                    'qty'           => array('type'=>'decimal'),
                    'weight'        => array('type'=>'decimal'),
                ),
            ),

            'creditmemo' => array(
                'entity_model'      => 'sales/order_creditmemo',
                //'table'=>'sales/creditmemo',
                'table'=>'sales/order_entity',
                'increment_model'=>'eav/entity_increment_numeric',
                'increment_per_store'=>true,
                'backend_prefix'=>'sales_entity/order_attribute_backend',
                'attributes' => array(
                    'entity_id'     => array(
                        'type'=>'static',
                        'backend'=>'sales_entity/order_creditmemo_attribute_backend_parent'
                    ),
                    'state'         => array('type'=>'int'),
                    'invoice_id'    => array('type'=>'int'),
                    'transaction_id'=> array(),

                    'order_id'      => array('type'=>'int'),
                    'creditmemo_status'     => array('type'=>'int'),
                    'billing_address_id'    => array('type'=>'int'),
                    'shipping_address_id'   => array('type'=>'int'),

                    'global_currency_code'    => array(),
                    'base_currency_code'    => array(),
                    'store_currency_code'   => array(),
                    'order_currency_code'   => array(),
                    'store_to_base_rate'    => array('type'=>'decimal'),
                    'store_to_order_rate'   => array('type'=>'decimal'),
                    'base_to_global_rate'    => array('type'=>'decimal'),
                    'base_to_order_rate'   => array('type'=>'decimal'),

                    'subtotal'          => array('type'=>'decimal'),
                    'discount_amount'   => array('type'=>'decimal'),
                    'tax_amount'        => array('type'=>'decimal'),
                    'shipping_amount'   => array('type'=>'decimal'),
                    'adjustment'        => array('type'=>'decimal'),
                    'adjustment_positive' => array('type'=>'decimal'),
                    'adjustment_negative' => array('type'=>'decimal'),
                    'grand_total'       => array('type'=>'decimal'),

                    'base_subtotal'          => array('type'=>'decimal'),
                    'base_discount_amount'   => array('type'=>'decimal'),
                    'base_tax_amount'        => array('type'=>'decimal'),
                    'base_shipping_amount'   => array('type'=>'decimal'),
                    'base_adjustment'        => array('type'=>'decimal'),
                    'base_adjustment_positive' => array('type'=>'decimal'),
                    'base_adjustment_negative' => array('type'=>'decimal'),
                    'base_grand_total'         => array('type'=>'decimal'),
                    'email_sent'               => array('type' => 'int'),
                    'store_id'                 => array('type' => 'static'),
                ),
            ),

            'creditmemo_item' => array(
                'entity_model'      => 'sales/order_creditmemo_item',
                //'table'=>'sales/creditmemo',
                'table'=>'sales/order_entity',
                'attributes' => array(
                    'parent_id'     => array(
                        'type'=>'static',
                        'backend'=>'sales_entity/order_creditmemo_attribute_backend_child'
                    ),
                    'order_item_id' => array('type'=>'int'),
                    'product_id'    => array('type'=>'int'),
                    'name'          => array(),
                    'description'   => array('type'=>'text'),
                    'sku'           => array(),
                    'qty'           => array('type'=>'decimal'),
                    'cost'          => array('type'=>'decimal'),
                    'price'         => array('type'=>'decimal'),
                    'discount_amount' => array('type'=>'decimal'),
                    'tax_amount'    => array('type'=>'decimal'),
                    'row_total'     => array('type'=>'decimal'),

                    'base_price'         => array('type'=>'decimal'),
                    'base_discount_amount' => array('type'=>'decimal'),
                    'base_tax_amount'    => array('type'=>'decimal'),
                    'base_row_total'     => array('type'=>'decimal'),

                    'additional_data'   => array('type'=>'text'),
                ),
            ),

            'creditmemo_comment' => array(
                'entity_model'      => 'sales/order_creditmemo_comment',
                'table'=>'sales/order_entity',
                'attributes' => array(
                    'parent_id' => array(
                        'type'=>'static',
                        'backend'=>'sales_entity/order_creditmemo_attribute_backend_child'
                    ),
                    'comment' => array('type'=>'text'),
                    'is_customer_notified' => array('type'=>'int'),
                ),
            ),

        );
    }
}
