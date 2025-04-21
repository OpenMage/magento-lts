<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/** @var Mage_Catalog_Model_Resource_Setup  $installer */
$installer = $this;
$installer->startSetup();

$installer->run("
    ALTER TABLE `{$installer->getTable('catalog_compare_item')}` add index `IDX_VISITOR_PRODUCTS` (`visitor_id`, `product_id`);
    ALTER TABLE `{$installer->getTable('catalog_compare_item')}` add index `IDX_CUSTOMER_PRODUCTS` (`customer_id`, `product_id`);
");

$installer->endSetup();
