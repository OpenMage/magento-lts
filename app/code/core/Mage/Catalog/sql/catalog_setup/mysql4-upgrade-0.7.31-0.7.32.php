<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Catalog
 */

/** @var Mage_Catalog_Model_Resource_Setup  $installer */
$installer = $this;

$installer->run("

update `{$installer->getTable('eav/attribute')}` set `is_required`=1 where `attribute_id`='{$installer->getAttributeId('catalog_product', 'tax_class_id')}'

");
