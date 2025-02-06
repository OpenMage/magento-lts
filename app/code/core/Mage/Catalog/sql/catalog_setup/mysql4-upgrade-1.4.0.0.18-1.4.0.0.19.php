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

$table = $this->getTable('catalog/category_product');

/**
 * Remove data duplicates
 */
$installer->getConnection()->changeColumn($table, 'position', 'position', "INT( 10 ) NOT NULL DEFAULT '0'");
