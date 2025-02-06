<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
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
