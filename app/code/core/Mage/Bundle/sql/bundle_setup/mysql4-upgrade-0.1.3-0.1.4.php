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
    UPDATE `{$installer->getTable('catalog/product')}` SET `has_options` = '1'
    WHERE (entity_id IN (
        SELECT parent_product_id FROM `{$installer->getTable('bundle/selection')}` GROUP BY parent_product_id
    ));
");

$installer->endSetup();
