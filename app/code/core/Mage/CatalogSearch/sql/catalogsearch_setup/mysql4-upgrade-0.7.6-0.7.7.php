<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_CatalogSearch
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer  = $this;
$installer->startSetup();

$table = $installer->getTable('catalogsearch_query');

$installer->getConnection()->changeColumn($table, 'synonim_for', 'synonym_for', 'VARCHAR( 255 ) NOT NULL');

$installer->endSetup();
