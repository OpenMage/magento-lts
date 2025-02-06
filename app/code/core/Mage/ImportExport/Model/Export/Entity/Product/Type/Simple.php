<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_ImportExport
 */

/**
 * Export entity product type simple model
 *
 * @category   Mage
 * @package    Mage_ImportExport
 */
class Mage_ImportExport_Model_Export_Entity_Product_Type_Simple extends Mage_ImportExport_Model_Export_Entity_Product_Type_Abstract
{
    /**
     * Overridden attributes parameters.
     *
     * @var array
     */
    protected $_attributeOverrides = [
        'has_options'      => ['source_model' => 'eav/entity_attribute_source_boolean'],
        'required_options' => ['source_model' => 'eav/entity_attribute_source_boolean'],
        'created_at'       => ['backend_type' => 'datetime'],
        'updated_at'       => ['backend_type' => 'datetime'],
    ];

    /**
     * Array of attributes codes which are disabled for export.
     *
     * @var array
     */
    protected $_disabledAttrs = [
        'old_id',
        'recurring_profile',
        'is_recurring',
        'tier_price',
        'group_price',
        'category_ids',
    ];
}
