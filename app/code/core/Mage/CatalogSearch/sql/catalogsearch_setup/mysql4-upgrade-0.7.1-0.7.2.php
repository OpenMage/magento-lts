<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_CatalogSearch
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;

$installer->run("
    ALTER TABLE {$this->getTable('catalogsearch_query')} ADD `display_in_terms` TINYINT( 1 ) NOT NULL DEFAULT '0';
");
