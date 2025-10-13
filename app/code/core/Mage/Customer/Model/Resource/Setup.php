<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/**
 * Customer resource setup model
 *
 * @package    Mage_Customer
 */
class Mage_Customer_Model_Resource_Setup extends Mage_Eav_Model_Entity_Setup
{
    /**
     * Prepare customer attribute values to save in additional table
     *
     * @param array $attr
     * @return array
     */
    protected function _prepareValues($attr)
    {
        $data = parent::_prepareValues($attr);

        return array_merge($data, [
            'is_visible'                => $this->_getValue($attr, 'visible', 1),
            'is_system'                 => $this->_getValue($attr, 'system', 1),
            'input_filter'              => $this->_getValue($attr, 'input_filter', null),
            'multiline_count'           => $this->_getValue($attr, 'multiline_count', 0),
            'validate_rules'            => $this->_getValue($attr, 'validate_rules', null),
            'data_model'                => $this->_getValue($attr, 'data', null),
            'sort_order'                => $this->_getValue($attr, 'position', 0),
        ]);
    }

    /**
     * Add customer attributes to customer forms
     */
    public function installCustomerForms()
    {
        $customer           = (int) $this->getEntityTypeId('customer');
        $customerAddress    = (int) $this->getEntityTypeId('customer_address');

        $attributeIds       = [];
        $select = $this->getConnection()->select()
            ->from(
                ['ea' => $this->getTable('eav/attribute')],
                ['entity_type_id', 'attribute_code', 'attribute_id'],
            )
            ->where('ea.entity_type_id IN(?)', [$customer, $customerAddress]);
        foreach ($this->getConnection()->fetchAll($select) as $row) {
            $attributeIds[$row['entity_type_id']][$row['attribute_code']] = $row['attribute_id'];
        }

        $data       = [];
        $entities   = $this->getDefaultEntities();
        $attributes = $entities['customer']['attributes'];
        foreach ($attributes as $attributeCode => $attribute) {
            $attributeId = $attributeIds[$customer][$attributeCode];
            $attribute['system'] = $attribute['system'] ?? true;
            $attribute['visible'] = $attribute['visible'] ?? true;
            if ($attribute['system'] != true || $attribute['visible'] != false) {
                $usedInForms = [
                    'customer_account_create',
                    'customer_account_edit',
                    'checkout_register',
                ];
                if (!empty($attribute['adminhtml_only'])) {
                    $usedInForms = ['adminhtml_customer'];
                } else {
                    $usedInForms[] = 'adminhtml_customer';
                }

                if (!empty($attribute['admin_checkout'])) {
                    $usedInForms[] = 'adminhtml_checkout';
                }

                foreach ($usedInForms as $formCode) {
                    $data[] = [
                        'form_code'     => $formCode,
                        'attribute_id'  => $attributeId,
                    ];
                }
            }
        }

        $attributes = $entities['customer_address']['attributes'];
        foreach ($attributes as $attributeCode => $attribute) {
            $attributeId = $attributeIds[$customerAddress][$attributeCode];
            $attribute['system'] = $attribute['system'] ?? true;
            $attribute['visible'] = $attribute['visible'] ?? true;
            if (($attribute['system'] == true && $attribute['visible'] == false) === false) {
                $usedInForms = [
                    'adminhtml_customer_address',
                    'customer_address_edit',
                    'customer_register_address',
                ];
                foreach ($usedInForms as $formCode) {
                    $data[] = [
                        'form_code'     => $formCode,
                        'attribute_id'  => $attributeId,
                    ];
                }
            }
        }

        if ($data) {
            $this->getConnection()->insertMultiple($this->getTable('customer/form_attribute'), $data);
        }
    }

    /**
     * Retrieve default entities: customer, customer_address
     *
     * @return array
     */
    public function getDefaultEntities()
    {
        return [
            'customer'                       => [
                'entity_model'                   => 'customer/customer',
                'attribute_model'                => 'customer/attribute',
                'table'                          => 'customer/entity',
                'increment_model'                => 'eav/entity_increment_numeric',
                'additional_attribute_table'     => 'customer/eav_attribute',
                'entity_attribute_collection'    => 'customer/attribute_collection',
                'attributes'                     => [
                    'website_id'         => [
                        'type'               => 'static',
                        'label'              => 'Associate to Website',
                        'input'              => 'select',
                        'source'             => 'customer/customer_attribute_source_website',
                        'backend'            => 'customer/customer_attribute_backend_website',
                        'sort_order'         => 10,
                        'position'           => 10,
                        'adminhtml_only'     => 1,
                    ],
                    'store_id'           => [
                        'type'               => 'static',
                        'label'              => 'Create In',
                        'input'              => 'select',
                        'source'             => 'customer/customer_attribute_source_store',
                        'backend'            => 'customer/customer_attribute_backend_store',
                        'sort_order'         => 20,
                        'visible'            => false,
                        'adminhtml_only'     => 1,
                    ],
                    'created_in'         => [
                        'type'               => 'varchar',
                        'label'              => 'Created From',
                        'input'              => 'text',
                        'required'           => false,
                        'sort_order'         => 20,
                        'position'           => 20,
                        'adminhtml_only'     => 1,
                    ],
                    'prefix'             => [
                        'type'               => 'varchar',
                        'label'              => 'Prefix',
                        'input'              => 'text',
                        'required'           => false,
                        'sort_order'         => 30,
                        'visible'            => false,
                        'system'             => false,
                        'position'           => 30,
                    ],
                    'firstname'          => [
                        'type'               => 'varchar',
                        'label'              => 'First Name',
                        'input'              => 'text',
                        'sort_order'         => 40,
                        'validate_rules'     => 'a:2:{s:15:"max_text_length";i:255;s:15:"min_text_length";i:1;}',
                        'position'           => 40,
                    ],
                    'middlename'         => [
                        'type'               => 'varchar',
                        'label'              => 'Middle Name/Initial',
                        'input'              => 'text',
                        'required'           => false,
                        'sort_order'         => 50,
                        'visible'            => true,
                        'system'             => false,
                        'position'           => 50,
                    ],
                    'lastname'           => [
                        'type'               => 'varchar',
                        'label'              => 'Last Name',
                        'input'              => 'text',
                        'sort_order'         => 60,
                        'validate_rules'     => 'a:2:{s:15:"max_text_length";i:255;s:15:"min_text_length";i:1;}',
                        'position'           => 60,
                    ],
                    'suffix'             => [
                        'type'               => 'varchar',
                        'label'              => 'Suffix',
                        'input'              => 'text',
                        'required'           => false,
                        'sort_order'         => 70,
                        'visible'            => false,
                        'system'             => false,
                        'position'           => 70,
                    ],
                    'email'              => [
                        'type'               => 'static',
                        'label'              => 'Email',
                        'input'              => 'text',
                        'sort_order'         => 80,
                        'validate_rules'     => 'a:1:{s:16:"input_validation";s:5:"email";}',
                        'position'           => 80,
                        'admin_checkout'    => 1,
                    ],
                    'group_id'           => [
                        'type'               => 'static',
                        'label'              => 'Group',
                        'input'              => 'select',
                        'source'             => 'customer/customer_attribute_source_group',
                        'sort_order'         => 25,
                        'position'           => 25,
                        'adminhtml_only'     => 1,
                        'admin_checkout'     => 1,
                    ],
                    'dob'                => [
                        'type'               => 'datetime',
                        'label'              => 'Date Of Birth',
                        'input'              => 'date',
                        'frontend'           => 'eav/entity_attribute_frontend_datetime',
                        'backend'            => 'eav/entity_attribute_backend_datetime',
                        'required'           => false,
                        'sort_order'         => 90,
                        'visible'            => false,
                        'system'             => false,
                        'input_filter'       => 'date',
                        'validate_rules'     => 'a:1:{s:16:"input_validation";s:4:"date";}',
                        'position'           => 90,
                        'admin_checkout'     => 1,
                    ],
                    'password_hash'      => [
                        'type'               => 'varchar',
                        'input'              => 'hidden',
                        'backend'            => 'customer/customer_attribute_backend_password',
                        'required'           => false,
                        'sort_order'         => 81,
                        'visible'            => false,
                    ],
                    'default_billing'    => [
                        'type'               => 'int',
                        'label'              => 'Default Billing Address',
                        'input'              => 'text',
                        'backend'            => 'customer/customer_attribute_backend_billing',
                        'required'           => false,
                        'sort_order'         => 82,
                        'visible'            => false,
                    ],
                    'default_shipping'   => [
                        'type'               => 'int',
                        'label'              => 'Default Shipping Address',
                        'input'              => 'text',
                        'backend'            => 'customer/customer_attribute_backend_shipping',
                        'required'           => false,
                        'sort_order'         => 83,
                        'visible'            => false,
                    ],
                    'taxvat'             => [
                        'type'               => 'varchar',
                        'label'              => 'Tax/VAT Number',
                        'input'              => 'text',
                        'required'           => false,
                        'sort_order'         => 100,
                        'visible'            => false,
                        'system'             => false,
                        'validate_rules'     => 'a:1:{s:15:"max_text_length";i:255;}',
                        'position'           => 100,
                        'admin_checkout'     => 1,
                    ],
                    'confirmation'       => [
                        'type'               => 'varchar',
                        'label'              => 'Is Confirmed',
                        'input'              => 'text',
                        'required'           => false,
                        'sort_order'         => 85,
                        'visible'            => false,
                    ],
                    'created_at'         => [
                        'type'               => 'static',
                        'label'              => 'Created At',
                        'input'              => 'date',
                        'required'           => false,
                        'sort_order'         => 86,
                        'visible'            => false,
                        'system'             => false,
                    ],
                    'gender'             => [
                        'type'               => 'int',
                        'label'              => 'Gender',
                        'input'              => 'select',
                        'source'             => 'eav/entity_attribute_source_table',
                        'required'           => false,
                        'sort_order'         => 110,
                        'visible'            => false,
                        'system'             => false,
                        'validate_rules'     => 'a:0:{}',
                        'position'           => 110,
                        'admin_checkout'     => 1,
                        'option'             => ['values' => ['Male', 'Female']],
                    ],
                ],
            ],

            'customer_address'               => [
                'entity_model'                   => 'customer/address',
                'attribute_model'                => 'customer/attribute',
                'table'                          => 'customer/address_entity',
                'additional_attribute_table'     => 'customer/eav_attribute',
                'entity_attribute_collection'    => 'customer/address_attribute_collection',
                'attributes'                     => [
                    'prefix'             => [
                        'type'               => 'varchar',
                        'label'              => 'Prefix',
                        'input'              => 'text',
                        'required'           => false,
                        'sort_order'         => 10,
                        'visible'            => false,
                        'system'             => false,
                        'position'           => 10,
                    ],
                    'firstname'          => [
                        'type'               => 'varchar',
                        'label'              => 'First Name',
                        'input'              => 'text',
                        'sort_order'         => 20,
                        'validate_rules'     => 'a:2:{s:15:"max_text_length";i:255;s:15:"min_text_length";i:1;}',
                        'position'           => 20,
                    ],
                    'middlename'         => [
                        'type'               => 'varchar',
                        'label'              => 'Middle Name/Initial',
                        'input'              => 'text',
                        'required'           => false,
                        'sort_order'         => 30,
                        'visible'            => true,
                        'system'             => false,
                        'position'           => 30,
                    ],
                    'lastname'           => [
                        'type'               => 'varchar',
                        'label'              => 'Last Name',
                        'input'              => 'text',
                        'sort_order'         => 40,
                        'validate_rules'     => 'a:2:{s:15:"max_text_length";i:255;s:15:"min_text_length";i:1;}',
                        'position'           => 40,
                    ],
                    'suffix'             => [
                        'type'               => 'varchar',
                        'label'              => 'Suffix',
                        'input'              => 'text',
                        'required'           => false,
                        'sort_order'         => 50,
                        'visible'            => false,
                        'system'             => false,
                        'position'           => 50,
                    ],
                    'company'            => [
                        'type'               => 'varchar',
                        'label'              => 'Company',
                        'input'              => 'text',
                        'required'           => false,
                        'sort_order'         => 60,
                        'validate_rules'     => 'a:2:{s:15:"max_text_length";i:255;s:15:"min_text_length";i:1;}',
                        'position'           => 60,
                    ],
                    'street'             => [
                        'type'               => 'text',
                        'label'              => 'Street Address',
                        'input'              => 'multiline',
                        'backend'            => 'customer/entity_address_attribute_backend_street',
                        'sort_order'         => 70,
                        'multiline_count'    => 2,
                        'validate_rules'     => 'a:2:{s:15:"max_text_length";i:255;s:15:"min_text_length";i:1;}',
                        'position'           => 70,
                    ],
                    'city'               => [
                        'type'               => 'varchar',
                        'label'              => 'City',
                        'input'              => 'text',
                        'sort_order'         => 80,
                        'validate_rules'     => 'a:2:{s:15:"max_text_length";i:255;s:15:"min_text_length";i:1;}',
                        'position'           => 80,
                    ],
                    'country_id'         => [
                        'type'               => 'varchar',
                        'label'              => 'Country',
                        'input'              => 'select',
                        'source'             => 'customer/entity_address_attribute_source_country',
                        'sort_order'         => 90,
                        'position'           => 90,
                    ],
                    'region'             => [
                        'type'               => 'varchar',
                        'label'              => 'State/Province',
                        'input'              => 'text',
                        'backend'            => 'customer/entity_address_attribute_backend_region',
                        'required'           => false,
                        'sort_order'         => 100,
                        'position'           => 100,
                    ],
                    'region_id'          => [
                        'type'               => 'int',
                        'label'              => 'State/Province',
                        'input'              => 'hidden',
                        'source'             => 'customer/entity_address_attribute_source_region',
                        'required'           => false,
                        'sort_order'         => 100,
                        'position'           => 100,
                    ],
                    'postcode'           => [
                        'type'               => 'varchar',
                        'label'              => 'Zip/Postal Code',
                        'input'              => 'text',
                        'sort_order'         => 110,
                        'validate_rules'     => 'a:0:{}',
                        'data'               => 'customer/attribute_data_postcode',
                        'position'           => 110,
                    ],
                    'telephone'          => [
                        'type'               => 'varchar',
                        'label'              => 'Telephone',
                        'input'              => 'text',
                        'sort_order'         => 120,
                        'validate_rules'     => 'a:2:{s:15:"max_text_length";i:255;s:15:"min_text_length";i:1;}',
                        'position'           => 120,
                    ],
                    'fax'                => [
                        'type'               => 'varchar',
                        'label'              => 'Fax',
                        'input'              => 'text',
                        'required'           => false,
                        'sort_order'         => 130,
                        'validate_rules'     => 'a:2:{s:15:"max_text_length";i:255;s:15:"min_text_length";i:1;}',
                        'position'           => 130,
                    ],
                ],
            ],
        ];
    }
}
