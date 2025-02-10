<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @var Mage_Catalog_Model_Resource_Setup  $installer
 */
$installer = $this;

$table = $this->getTable('catalog/category_product');

/**
 * Remove data duplicates
 */
$installer->getConnection()->changeColumn($table, 'position', 'position', "INT( 10 ) NOT NULL DEFAULT '0'");
