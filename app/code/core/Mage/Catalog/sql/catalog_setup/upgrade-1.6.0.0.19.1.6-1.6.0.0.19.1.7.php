<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Catalog
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->run("ALTER TABLE {$this->getTable('catalog/product_website')} ROW_FORMAT=DYNAMIC;");
$installer->run("ALTER TABLE {$this->getTable('catalog/product_relation')} ROW_FORMAT=DYNAMIC;");

$installer->endSetup();
