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
$installer->startSetup();

$installer->getConnection()->changeColumn($installer->getTable('catalog/product_option_title'), 'title', 'title', 'VARCHAR(255) NOT NULL default \'\'');
$installer->getConnection()->changeColumn($installer->getTable('catalog/product_option_type_title'), 'title', 'title', 'VARCHAR(255) NOT NULL default \'\'');

$installer->endSetup();
