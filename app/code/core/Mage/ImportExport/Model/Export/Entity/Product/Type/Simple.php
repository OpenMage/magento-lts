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
 * @package     Mage_ImportExport
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Export entity product type simple model
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_ImportExport_Model_Export_Entity_Product_Type_Simple
    extends Mage_ImportExport_Model_Export_Entity_Product_Type_Abstract
{
    /**
     * Overriden attributes parameters.
     *
     * @var array
     */
    protected $_attributeOverrides = array(
        'has_options'      => array('source_model' => 'eav/entity_attribute_source_boolean'),
        'required_options' => array('source_model' => 'eav/entity_attribute_source_boolean'),
        'created_at'       => array('backend_type' => 'datetime'),
        'updated_at'       => array('backend_type' => 'datetime')
    );

    /**
     * Array of attributes codes which are disabled for export.
     *
     * @var array
     */
    protected $_disabledAttrs = array(
        'old_id',
        'recurring_profile',
        'is_recurring',
        'tier_price',
        'group_price',
        'category_ids'
    );
}
