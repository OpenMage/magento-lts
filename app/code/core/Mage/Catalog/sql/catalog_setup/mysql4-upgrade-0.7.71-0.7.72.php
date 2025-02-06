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

$installer->removeAttribute('catalog_product', 'category_ids');
$installer->getConnection()->dropColumn($installer->getTable('catalog/product'), 'category_ids');

$installer->endSetup();
