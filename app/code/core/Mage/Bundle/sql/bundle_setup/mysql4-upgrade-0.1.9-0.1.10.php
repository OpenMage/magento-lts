<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Bundle
 */

/** @var Mage_Catalog_Model_Resource_Setup  $installer */
$installer = $this;
$installer->startSetup();

$installer->run("
INSERT IGNORE INTO `{$installer->getTable('catalog/product_relation')}`
SELECT
  `parent_product_id`,
  `product_id`
FROM `{$installer->getTable('bundle/selection')}`;
");

$installer->endSetup();
