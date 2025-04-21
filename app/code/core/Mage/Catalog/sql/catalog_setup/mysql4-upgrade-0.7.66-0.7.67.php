<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/** @var Mage_Catalog_Model_Resource_Setup  $installer */
$installer = $this;

$installer->addAttribute('catalog_product', 'created_at', [
    'type'      => 'static',
    'backend'   => 'eav/entity_attribute_backend_time_created',
    'visible'   => 0,
]);
$installer->addAttribute('catalog_product', 'updated_at', [
    'type'      => 'static',
    'backend'   => 'eav/entity_attribute_backend_time_updated',
    'visible'   => 0,
]);
