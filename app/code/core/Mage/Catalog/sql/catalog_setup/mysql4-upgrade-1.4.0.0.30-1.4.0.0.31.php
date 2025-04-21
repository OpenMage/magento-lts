<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/** @var Mage_Catalog_Model_Resource_Setup  $installer */
$installer = $this;

$installer->addAttribute('catalog_category', 'filter_price_range', [
    'group'         => 'Display Settings',
    'type'          => 'int',
    'label'         => 'Layered Navigation Price Step',
    'required'      => false,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'       => 1,
    'input_renderer' => 'adminhtml/catalog_category_helper_pricestep',
]);
