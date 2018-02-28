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
 * @package     Mage_Catalog
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog entity setup
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Resource_Setup extends Mage_Eav_Model_Entity_Setup
{
    /**
     * Prepare catalog attribute values to save
     *
     * @param array $attr
     * @return array
     */
    protected function _prepareValues($attr)
    {
        $data = parent::_prepareValues($attr);
        $data = array_merge($data, array(
            'frontend_input_renderer'       => $this->_getValue($attr, 'input_renderer'),
            'is_global'                     => $this->_getValue(
                $attr,
                'global',
                Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL
            ),
            'is_visible'                    => $this->_getValue($attr, 'visible', 1),
            'is_searchable'                 => $this->_getValue($attr, 'searchable', 0),
            'is_filterable'                 => $this->_getValue($attr, 'filterable', 0),
            'is_comparable'                 => $this->_getValue($attr, 'comparable', 0),
            'is_visible_on_front'           => $this->_getValue($attr, 'visible_on_front', 0),
            'is_wysiwyg_enabled'            => $this->_getValue($attr, 'wysiwyg_enabled', 0),
            'is_html_allowed_on_front'      => $this->_getValue($attr, 'is_html_allowed_on_front', 0),
            'is_visible_in_advanced_search' => $this->_getValue($attr, 'visible_in_advanced_search', 0),
            'is_filterable_in_search'       => $this->_getValue($attr, 'filterable_in_search', 0),
            'used_in_product_listing'       => $this->_getValue($attr, 'used_in_product_listing', 0),
            'used_for_sort_by'              => $this->_getValue($attr, 'used_for_sort_by', 0),
            'apply_to'                      => $this->_getValue($attr, 'apply_to'),
            'position'                      => $this->_getValue($attr, 'position', 0),
            'is_configurable'               => $this->_getValue($attr, 'is_configurable', 1),
            'is_used_for_promo_rules'       => $this->_getValue($attr, 'used_for_promo_rules', 0)
        ));
        return $data;
    }

    /**
     * Default entites and attributes
     *
     * @return array
     */
    public function getDefaultEntities()
    {
        return array(
            'catalog_category'               => array(
                'entity_model'                   => 'catalog/category',
                'attribute_model'                => 'catalog/resource_eav_attribute',
                'table'                          => 'catalog/category',
                'additional_attribute_table'     => 'catalog/eav_attribute',
                'entity_attribute_collection'    => 'catalog/category_attribute_collection',
                'default_group'                  => 'General Information',
                'attributes'                     => array(
                    'name'               => array(
                        'type'                       => 'varchar',
                        'label'                      => 'Name',
                        'input'                      => 'text',
                        'sort_order'                 => 1,
                        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'group'                      => 'General Information',
                    ),
                    'is_active'          => array(
                        'type'                       => 'int',
                        'label'                      => 'Is Active',
                        'input'                      => 'select',
                        'source'                     => 'eav/entity_attribute_source_boolean',
                        'sort_order'                 => 2,
                        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'group'                      => 'General Information',
                    ),
                    'url_key'            => array(
                        'type'                       => 'varchar',
                        'label'                      => 'URL Key',
                        'input'                      => 'text',
                        'backend'                    => 'catalog/category_attribute_backend_urlkey',
                        'required'                   => false,
                        'sort_order'                 => 3,
                        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'group'                      => 'General Information',
                    ),
                    'description'        => array(
                        'type'                       => 'text',
                        'label'                      => 'Description',
                        'input'                      => 'textarea',
                        'required'                   => false,
                        'sort_order'                 => 4,
                        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'wysiwyg_enabled'            => true,
                        'is_html_allowed_on_front'   => true,
                        'group'                      => 'General Information',
                    ),
                    'image'              => array(
                        'type'                       => 'varchar',
                        'label'                      => 'Image',
                        'input'                      => 'image',
                        'backend'                    => 'catalog/category_attribute_backend_image',
                        'required'                   => false,
                        'sort_order'                 => 5,
                        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'group'                      => 'General Information',
                    ),
                    'meta_title'         => array(
                        'type'                       => 'varchar',
                        'label'                      => 'Page Title',
                        'input'                      => 'text',
                        'required'                   => false,
                        'sort_order'                 => 6,
                        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'group'                      => 'General Information',
                    ),
                    'meta_keywords'      => array(
                        'type'                       => 'text',
                        'label'                      => 'Meta Keywords',
                        'input'                      => 'textarea',
                        'required'                   => false,
                        'sort_order'                 => 7,
                        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'group'                      => 'General Information',
                    ),
                    'meta_description'   => array(
                        'type'                       => 'text',
                        'label'                      => 'Meta Description',
                        'input'                      => 'textarea',
                        'required'                   => false,
                        'sort_order'                 => 8,
                        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'group'                      => 'General Information',
                    ),
                    'display_mode'       => array(
                        'type'                       => 'varchar',
                        'label'                      => 'Display Mode',
                        'input'                      => 'select',
                        'source'                     => 'catalog/category_attribute_source_mode',
                        'required'                   => false,
                        'sort_order'                 => 10,
                        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'group'                      => 'Display Settings',
                    ),
                    'landing_page'       => array(
                        'type'                       => 'int',
                        'label'                      => 'CMS Block',
                        'input'                      => 'select',
                        'source'                     => 'catalog/category_attribute_source_page',
                        'required'                   => false,
                        'sort_order'                 => 20,
                        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'group'                      => 'Display Settings',
                    ),
                    'is_anchor'          => array(
                        'type'                       => 'int',
                        'label'                      => 'Is Anchor',
                        'input'                      => 'select',
                        'source'                     => 'eav/entity_attribute_source_boolean',
                        'required'                   => false,
                        'sort_order'                 => 30,
                        'group'                      => 'Display Settings',
                    ),
                    'path'               => array(
                        'type'                       => 'static',
                        'label'                      => 'Path',
                        'required'                   => false,
                        'sort_order'                 => 12,
                        'visible'                    => false,
                        'group'                      => 'General Information',
                    ),
                    'position'           => array(
                        'type'                       => 'static',
                        'label'                      => 'Position',
                        'required'                   => false,
                        'sort_order'                 => 13,
                        'visible'                    => false,
                        'group'                      => 'General Information',
                    ),
                    'all_children'       => array(
                        'type'                       => 'text',
                        'required'                   => false,
                        'sort_order'                 => 14,
                        'visible'                    => false,
                        'group'                      => 'General Information',
                    ),
                    'path_in_store'      => array(
                        'type'                       => 'text',
                        'required'                   => false,
                        'sort_order'                 => 15,
                        'visible'                    => false,
                        'group'                      => 'General Information',
                    ),
                    'children'           => array(
                        'type'                       => 'text',
                        'required'                   => false,
                        'sort_order'                 => 16,
                        'visible'                    => false,
                        'group'                      => 'General Information',
                    ),
                    'url_path'           => array(
                        'type'                       => 'varchar',
                        'required'                   => false,
                        'sort_order'                 => 17,
                        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'visible'                    => false,
                        'group'                      => 'General Information',
                    ),
                    'custom_design'      => array(
                        'type'                       => 'varchar',
                        'label'                      => 'Custom Design',
                        'input'                      => 'select',
                        'source'                     => 'core/design_source_design',
                        'required'                   => false,
                        'sort_order'                 => 10,
                        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'group'                      => 'Custom Design',
                    ),
                    'custom_design_from' => array(
                        'type'                       => 'datetime',
                        'label'                      => 'Active From',
                        'input'                      => 'date',
                        'backend'                    => 'eav/entity_attribute_backend_datetime',
                        'required'                   => false,
                        'sort_order'                 => 30,
                        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'group'                      => 'Custom Design',
                    ),
                    'custom_design_to'   => array(
                        'type'                       => 'datetime',
                        'label'                      => 'Active To',
                        'input'                      => 'date',
                        'backend'                    => 'eav/entity_attribute_backend_datetime',
                        'required'                   => false,
                        'sort_order'                 => 40,
                        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'group'                      => 'Custom Design',
                    ),
                    'page_layout'        => array(
                        'type'                       => 'varchar',
                        'label'                      => 'Page Layout',
                        'input'                      => 'select',
                        'source'                     => 'catalog/category_attribute_source_layout',
                        'required'                   => false,
                        'sort_order'                 => 50,
                        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'group'                      => 'Custom Design',
                    ),
                    'custom_layout_update' => array(
                        'type'                       => 'text',
                        'label'                      => 'Custom Layout Update',
                        'input'                      => 'textarea',
                        'backend'                    => 'catalog/attribute_backend_customlayoutupdate',
                        'required'                   => false,
                        'sort_order'                 => 60,
                        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'group'                      => 'Custom Design',
                    ),
                    'level'              => array(
                        'type'                       => 'static',
                        'label'                      => 'Level',
                        'required'                   => false,
                        'sort_order'                 => 24,
                        'visible'                    => false,
                        'group'                      => 'General Information',
                    ),
                    'children_count'     => array(
                        'type'                       => 'static',
                        'label'                      => 'Children Count',
                        'required'                   => false,
                        'sort_order'                 => 25,
                        'visible'                    => false,
                        'group'                      => 'General Information',
                    ),
                    'available_sort_by'  => array(
                        'type'                       => 'text',
                        'label'                      => 'Available Product Listing Sort By',
                        'input'                      => 'multiselect',
                        'source'                     => 'catalog/category_attribute_source_sortby',
                        'backend'                    => 'catalog/category_attribute_backend_sortby',
                        'sort_order'                 => 40,
                        'input_renderer'             => 'adminhtml/catalog_category_helper_sortby_available',
                        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'group'                      => 'Display Settings',
                    ),
                    'default_sort_by'    => array(
                        'type'                       => 'varchar',
                        'label'                      => 'Default Product Listing Sort By',
                        'input'                      => 'select',
                        'source'                     => 'catalog/category_attribute_source_sortby',
                        'backend'                    => 'catalog/category_attribute_backend_sortby',
                        'sort_order'                 => 50,
                        'input_renderer'             => 'adminhtml/catalog_category_helper_sortby_default',
                        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'group'                      => 'Display Settings',
                    ),
                    'include_in_menu'    => array(
                        'type'                       => 'int',
                        'label'                      => 'Include in Navigation Menu',
                        'input'                      => 'select',
                        'source'                     => 'eav/entity_attribute_source_boolean',
                        'default'                    => '1',
                        'sort_order'                 => 10,
                        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'group'                      => 'General Information',
                    ),
                    'custom_use_parent_settings' => array(
                        'type'                       => 'int',
                        'label'                      => 'Use Parent Category Settings',
                        'input'                      => 'select',
                        'source'                     => 'eav/entity_attribute_source_boolean',
                        'required'                   => false,
                        'sort_order'                 => 5,
                        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'group'                      => 'Custom Design',
                    ),
                    'custom_apply_to_products' => array(
                        'type'                       => 'int',
                        'label'                      => 'Apply To Products',
                        'input'                      => 'select',
                        'source'                     => 'eav/entity_attribute_source_boolean',
                        'required'                   => false,
                        'sort_order'                 => 6,
                        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'group'                      => 'Custom Design',
                    ),
                    'filter_price_range' => array(
                        'type'                       => 'decimal',
                        'label'                      => 'Layered Navigation Price Step',
                        'input'                      => 'text',
                        'required'                   => false,
                        'sort_order'                 => 51,
                        'input_renderer'             => 'adminhtml/catalog_category_helper_pricestep',
                        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'group'                      => 'Display Settings',
                    ),
                )
            ),
            'catalog_product'                => array(
                'entity_model'                   => 'catalog/product',
                'attribute_model'                => 'catalog/resource_eav_attribute',
                'table'                          => 'catalog/product',
                'additional_attribute_table'     => 'catalog/eav_attribute',
                'entity_attribute_collection'    => 'catalog/product_attribute_collection',
                'attributes'                     => array(
                    'name'               => array(
                        'type'                       => 'varchar',
                        'label'                      => 'Name',
                        'input'                      => 'text',
                        'sort_order'                 => 1,
                        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'searchable'                 => true,
                        'visible_in_advanced_search' => true,
                        'used_in_product_listing'    => true,
                        'used_for_sort_by'           => true,
                    ),
                    'description'        => array(
                        'type'                       => 'text',
                        'label'                      => 'Description',
                        'input'                      => 'textarea',
                        'sort_order'                 => 2,
                        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'searchable'                 => true,
                        'comparable'                 => true,
                        'wysiwyg_enabled'            => true,
                        'is_html_allowed_on_front'   => true,
                        'visible_in_advanced_search' => true,
                    ),
                    'short_description'  => array(
                        'type'                       => 'text',
                        'label'                      => 'Short Description',
                        'input'                      => 'textarea',
                        'sort_order'                 => 3,
                        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'searchable'                 => true,
                        'comparable'                 => true,
                        'wysiwyg_enabled'            => true,
                        'is_html_allowed_on_front'   => true,
                        'visible_in_advanced_search' => true,
                        'used_in_product_listing'    => true,
                    ),
                    'sku'                => array(
                        'type'                       => 'static',
                        'label'                      => 'SKU',
                        'input'                      => 'text',
                        'backend'                    => 'catalog/product_attribute_backend_sku',
                        'unique'                     => true,
                        'sort_order'                 => 4,
                        'searchable'                 => true,
                        'comparable'                 => true,
                        'visible_in_advanced_search' => true,
                    ),
                    'price'              => array(
                        'type'                       => 'decimal',
                        'label'                      => 'Price',
                        'input'                      => 'price',
                        'backend'                    => 'catalog/product_attribute_backend_price',
                        'sort_order'                 => 1,
                        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
                        'searchable'                 => true,
                        'filterable'                 => true,
                        'visible_in_advanced_search' => true,
                        'used_in_product_listing'    => true,
                        'used_for_sort_by'           => true,
                        'apply_to'                   => 'simple,configurable,virtual',
                        'group'                      => 'Prices',
                    ),
                    'special_price'      => array(
                        'type'                       => 'decimal',
                        'label'                      => 'Special Price',
                        'input'                      => 'price',
                        'backend'                    => 'catalog/product_attribute_backend_price',
                        'required'                   => false,
                        'sort_order'                 => 2,
                        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
                        'used_in_product_listing'    => true,
                        'apply_to'                   => 'simple,configurable,virtual',
                        'group'                      => 'Prices',
                    ),
                    'special_from_date'  => array(
                        'type'                       => 'datetime',
                        'label'                      => 'Special Price From Date',
                        'input'                      => 'date',
                        'backend'                    => 'catalog/product_attribute_backend_startdate',
                        'required'                   => false,
                        'sort_order'                 => 3,
                        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
                        'used_in_product_listing'    => true,
                        'apply_to'                   => 'simple,configurable,virtual',
                        'group'                      => 'Prices',
                    ),
                    'special_to_date'    => array(
                        'type'                       => 'datetime',
                        'label'                      => 'Special Price To Date',
                        'input'                      => 'date',
                        'backend'                    => 'eav/entity_attribute_backend_datetime',
                        'required'                   => false,
                        'sort_order'                 => 4,
                        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
                        'used_in_product_listing'    => true,
                        'apply_to'                   => 'simple,configurable,virtual',
                        'group'                      => 'Prices',
                    ),
                    'cost'               => array(
                        'type'                       => 'decimal',
                        'label'                      => 'Cost',
                        'input'                      => 'price',
                        'backend'                    => 'catalog/product_attribute_backend_price',
                        'required'                   => false,
                        'user_defined'               => true,
                        'sort_order'                 => 5,
                        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
                        'apply_to'                   => 'simple,virtual',
                        'group'                      => 'Prices',
                    ),
                    'weight'             => array(
                        'type'                       => 'decimal',
                        'label'                      => 'Weight',
                        'input'                      => 'weight',
                        'sort_order'                 => 5,
                        'apply_to'                   => Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
                    ),
                    'manufacturer'       => array(
                        'type'                       => 'int',
                        'label'                      => 'Manufacturer',
                        'input'                      => 'select',
                        'required'                   => false,
                        'user_defined'               => true,
                        'searchable'                 => true,
                        'filterable'                 => true,
                        'comparable'                 => true,
                        'visible_in_advanced_search' => true,
                        'apply_to'                   => Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
                    ),
                    'meta_title'         => array(
                        'type'                       => 'varchar',
                        'label'                      => 'Meta Title',
                        'input'                      => 'text',
                        'required'                   => false,
                        'sort_order'                 => 1,
                        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'group'                      => 'Meta Information',
                    ),
                    'meta_keyword'       => array(
                        'type'                       => 'text',
                        'label'                      => 'Meta Keywords',
                        'input'                      => 'textarea',
                        'required'                   => false,
                        'sort_order'                 => 2,
                        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'group'                      => 'Meta Information',
                    ),
                    'meta_description'   => array(
                        'type'                       => 'varchar',
                        'label'                      => 'Meta Description',
                        'input'                      => 'textarea',
                        'required'                   => false,
                        'note'                       => 'Maximum 255 chars',
                        'class'                      => 'validate-length maximum-length-255',
                        'sort_order'                 => 3,
                        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'group'                      => 'Meta Information',
                    ),
                    'image'              => array(
                        'type'                       => 'varchar',
                        'label'                      => 'Base Image',
                        'input'                      => 'media_image',
                        'frontend'                   => 'catalog/product_attribute_frontend_image',
                        'required'                   => false,
                        'sort_order'                 => 1,
                        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'group'                      => 'Images',
                    ),
                    'small_image'        => array(
                        'type'                       => 'varchar',
                        'label'                      => 'Small Image',
                        'input'                      => 'media_image',
                        'frontend'                   => 'catalog/product_attribute_frontend_image',
                        'required'                   => false,
                        'sort_order'                 => 2,
                        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'used_in_product_listing'    => true,
                        'group'                      => 'Images',
                    ),
                    'thumbnail'          => array(
                        'type'                       => 'varchar',
                        'label'                      => 'Thumbnail',
                        'input'                      => 'media_image',
                        'frontend'                   => 'catalog/product_attribute_frontend_image',
                        'required'                   => false,
                        'sort_order'                 => 3,
                        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'used_in_product_listing'    => true,
                        'group'                      => 'Images',
                    ),
                    'media_gallery'      => array(
                        'type'                       => 'varchar',
                        'label'                      => 'Media Gallery',
                        'input'                      => 'gallery',
                        'backend'                    => 'catalog/product_attribute_backend_media',
                        'required'                   => false,
                        'sort_order'                 => 4,
                        'group'                      => 'Images',
                    ),
                    'old_id'             => array(
                        'type'                       => 'int',
                        'required'                   => false,
                        'sort_order'                 => 6,
                        'visible'                    => false,
                    ),
                    'group_price'         => array(
                        'type'                       => 'decimal',
                        'label'                      => 'Group Price',
                        'input'                      => 'text',
                        'backend'                    => 'catalog/product_attribute_backend_groupprice',
                        'required'                   => false,
                        'sort_order'                 => 6,
                        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
                        'apply_to'                   => 'simple,configurable,virtual',
                        'group'                      => 'Prices',
                    ),
                    'tier_price'         => array(
                        'type'                       => 'decimal',
                        'label'                      => 'Tier Price',
                        'input'                      => 'text',
                        'backend'                    => 'catalog/product_attribute_backend_tierprice',
                        'required'                   => false,
                        'sort_order'                 => 6,
                        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
                        'apply_to'                   => 'simple,configurable,virtual',
                        'group'                      => 'Prices',
                    ),
                    'color'              => array(
                        'type'                       => 'int',
                        'label'                      => 'Color',
                        'input'                      => 'select',
                        'required'                   => false,
                        'user_defined'               => true,
                        'searchable'                 => true,
                        'filterable'                 => true,
                        'comparable'                 => true,
                        'visible_in_advanced_search' => true,
                        'apply_to'                   => Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
                    ),
                    'news_from_date'     => array(
                        'type'                       => 'datetime',
                        'label'                      => 'Set Product as New from Date',
                        'input'                      => 'date',
                        'backend'                    => 'eav/entity_attribute_backend_datetime',
                        'required'                   => false,
                        'sort_order'                 => 7,
                        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
                        'used_in_product_listing'    => true,
                    ),
                    'news_to_date'       => array(
                        'type'                       => 'datetime',
                        'label'                      => 'Set Product as New to Date',
                        'input'                      => 'date',
                        'backend'                    => 'eav/entity_attribute_backend_datetime',
                        'required'                   => false,
                        'sort_order'                 => 8,
                        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
                        'used_in_product_listing'    => true,
                    ),
                    'gallery'            => array(
                        'type'                       => 'varchar',
                        'label'                      => 'Image Gallery',
                        'input'                      => 'gallery',
                        'required'                   => false,
                        'sort_order'                 => 5,
                        'group'                      => 'Images',
                    ),
                    'status'             => array(
                        'type'                       => 'int',
                        'label'                      => 'Status',
                        'input'                      => 'select',
                        'source'                     => 'catalog/product_status',
                        'sort_order'                 => 9,
                        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
                        'searchable'                 => true,
                        'used_in_product_listing'    => true,
                    ),
                    'url_key'            => array(
                        'type'                       => 'varchar',
                        'label'                      => 'URL Key',
                        'input'                      => 'text',
                        'backend'                    => 'catalog/product_attribute_backend_urlkey',
                        'required'                   => false,
                        'sort_order'                 => 10,
                        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'used_in_product_listing'    => true,
                    ),
                    'url_path'           => array(
                        'type'                       => 'varchar',
                        'required'                   => false,
                        'sort_order'                 => 11,
                        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'visible'                    => false,
                    ),
                    'minimal_price'      => array(
                        'type'                       => 'decimal',
                        'label'                      => 'Minimal Price',
                        'input'                      => 'price',
                        'required'                   => false,
                        'sort_order'                 => 7,
                        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'visible'                    => false,
                        'apply_to'                   => 'simple,configurable,virtual',
                        'group'                      => 'Prices',
                    ),
                    'is_recurring'       => array(
                        'type'                       => 'int',
                        'label'                      => 'Enable Recurring Profile',
                        'input'                      => 'select',
                        'source'                     => 'eav/entity_attribute_source_boolean',
                        'required'                   => false,
                        'note'                       =>
                            'Products with recurring profile participate in catalog as nominal items.',
                        'sort_order'                 => 1,
                        'apply_to'                   => 'simple,virtual',
                        'is_configurable'            => false,
                        'group'                      => 'Recurring Profile',
                    ),
                    'recurring_profile'  => array(
                        'type'                       => 'text',
                        'label'                      => 'Recurring Payment Profile',
                        'input'                      => 'text',
                        'backend'                    => 'catalog/product_attribute_backend_recurring',
                        'required'                   => false,
                        'sort_order'                 => 2,
                        'apply_to'                   => 'simple,virtual',
                        'is_configurable'            => false,
                        'group'                      => 'Recurring Profile',
                    ),
                    'visibility'         => array(
                        'type'                       => 'int',
                        'label'                      => 'Visibility',
                        'input'                      => 'select',
                        'source'                     => 'catalog/product_visibility',
                        'default'                    => '4',
                        'sort_order'                 => 12,
                        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                    ),
                    'custom_design'      => array(
                        'type'                       => 'varchar',
                        'label'                      => 'Custom Design',
                        'input'                      => 'select',
                        'source'                     => 'core/design_source_design',
                        'required'                   => false,
                        'sort_order'                 => 1,
                        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'group'                      => 'Design',
                    ),
                    'custom_design_from' => array(
                        'type'                       => 'datetime',
                        'label'                      => 'Active From',
                        'input'                      => 'date',
                        'backend'                    => 'eav/entity_attribute_backend_datetime',
                        'required'                   => false,
                        'sort_order'                 => 2,
                        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'group'                      => 'Design',
                    ),
                    'custom_design_to'   => array(
                        'type'                       => 'datetime',
                        'label'                      => 'Active To',
                        'input'                      => 'date',
                        'backend'                    => 'eav/entity_attribute_backend_datetime',
                        'required'                   => false,
                        'sort_order'                 => 3,
                        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'group'                      => 'Design',
                    ),
                    'custom_layout_update' => array(
                        'type'                       => 'text',
                        'label'                      => 'Custom Layout Update',
                        'input'                      => 'textarea',
                        'backend'                    => 'catalog/attribute_backend_customlayoutupdate',
                        'required'                   => false,
                        'sort_order'                 => 4,
                        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'group'                      => 'Design',
                    ),
                    'page_layout'        => array(
                        'type'                       => 'varchar',
                        'label'                      => 'Page Layout',
                        'input'                      => 'select',
                        'source'                     => 'catalog/product_attribute_source_layout',
                        'required'                   => false,
                        'sort_order'                 => 5,
                        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'group'                      => 'Design',
                    ),
                    'category_ids'       => array(
                        'type'                       => 'static',
                        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                        'required'                   => false,
                        'sort_order'                 => 13,
                        'visible'                    => false,
                    ),
                    'options_container'  => array(
                        'type'                       => 'varchar',
                        'label'                      => 'Display Product Options In',
                        'input'                      => 'select',
                        'source'                     => 'catalog/entity_product_attribute_design_options_container',
                        'required'                   => false,
                        'default'                    => 'container1',
                        'sort_order'                 => 6,
                        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'group'                      => 'Design',
                    ),
                    'required_options'   => array(
                        'type'                       => 'static',
                        'input'                      => 'text',
                        'required'                   => false,
                        'sort_order'                 => 14,
                        'visible'                    => false,
                        'used_in_product_listing'    => true,
                    ),
                    'has_options'        => array(
                        'type'                       => 'static',
                        'input'                      => 'text',
                        'required'                   => false,
                        'sort_order'                 => 15,
                        'visible'                    => false,
                    ),
                    'image_label'        => array(
                        'type'                       => 'varchar',
                        'label'                      => 'Image Label',
                        'input'                      => 'text',
                        'required'                   => false,
                        'sort_order'                 => 16,
                        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'visible'                    => false,
                        'used_in_product_listing'    => true,
                        'is_configurable'            => false,
                    ),
                    'small_image_label'  => array(
                        'type'                       => 'varchar',
                        'label'                      => 'Small Image Label',
                        'input'                      => 'text',
                        'required'                   => false,
                        'sort_order'                 => 17,
                        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'visible'                    => false,
                        'used_in_product_listing'    => true,
                        'is_configurable'            => false,
                    ),
                    'thumbnail_label'    => array(
                        'type'                       => 'varchar',
                        'label'                      => 'Thumbnail Label',
                        'input'                      => 'text',
                        'required'                   => false,
                        'sort_order'                 => 18,
                        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'visible'                    => false,
                        'used_in_product_listing'    => true,
                        'is_configurable'            => false,
                    ),
                    'created_at'         => array(
                        'type'                       => 'static',
                        'input'                      => 'text',
                        'backend'                    => 'eav/entity_attribute_backend_time_created',
                        'sort_order'                 => 19,
                        'visible'                    => false,
                    ),
                    'updated_at'         => array(
                        'type'                       => 'static',
                        'input'                      => 'text',
                        'backend'                    => 'eav/entity_attribute_backend_time_updated',
                        'sort_order'                 => 20,
                        'visible'                    => false,
                    ),
                )
            )
        );
    }

    /**
     * Converts old tree to new
     *
     * @deprecated since 1.5.0.0
     * @return Mage_Catalog_Model_Resource_Setup
     */
    public function convertOldTreeToNew()
    {
        if (!Mage::getModel('catalog/category')->load(1)->getId()) {
            Mage::getModel('catalog/category')->setId(1)->setPath(1)->save();
        }

        $categories = array();

        $select = $this->getConnection()->select();
        $select->from($this->getTable('catalog/category'));
        $categories = $this->getConnection()->fetchAll($select);

        if (is_array($categories)) {
            foreach ($categories as $category) {
                $path = $this->_getCategoryPath($category);
                $path = array_reverse($path);
                $path = implode('/', $path);
                if ($category['entity_id'] != 1 && substr($path, 0, 2) != '1/') {
                    $path = "1/{$path}";
                }

                $this
                    ->getConnection()
                    ->update(
                        $this->getTable('catalog/category'),
                        array('path' => $path),
                        array('entity_id = ?' => $category['entity_id'])
                    );
            }
        }
        return $this;
    }

    /**
     * Returns category entity row by category id
     *
     * @param int $entityId
     * @return array
     */
    protected function _getCategoryEntityRow($entityId)
    {
        $select = $this->getConnection()->select();

        $select->from($this->getTable('catalog/category'));
        $select->where('entity_id = :entity_id');

        return $this->getConnection()->fetchRow($select, array('entity_id' => $entityId));
    }

    /**
     * Returns category path as array
     *
     * @param array $category
     * @param array $path
     * @return string
     */
    protected function _getCategoryPath($category, $path = array())
    {
        $path[] = $category['entity_id'];

        if ($category['parent_id'] != 0) {
            $parentCategory = $this->_getCategoryEntityRow($category['parent_id']);
            if ($parentCategory) {
                $path = $this->_getCategoryPath($parentCategory, $path);
            }
        }

        return $path;
    }

    /**
     * Creates level values for categories and saves them
     *
     * @deprecated since 1.5.0.0
     * @return Mage_Catalog_Model_Resource_Setup
     */
    public function rebuildCategoryLevels()
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
            ->from($this->getTable('catalog/category'));

        $categories = $adapter->fetchAll($select);

        foreach ($categories as $category) {
            $level = count(explode('/', $category['path']))-1;
            $adapter->update(
                $this->getTable('catalog/category'),
                array('level' => $level),
                array('entity_id = ?' => $category['entity_id'])
            );
        }
        return $this;
    }
}
