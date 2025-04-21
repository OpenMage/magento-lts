<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/** @var Mage_Catalog_Model_Resource_Setup  $installer */
$installer = $this;

$installer->run("

update `{$installer->getTable('eav/attribute')}` set `is_required`=1 where `attribute_id`='{$installer->getAttributeId('catalog_product', 'tax_class_id')}'

");
