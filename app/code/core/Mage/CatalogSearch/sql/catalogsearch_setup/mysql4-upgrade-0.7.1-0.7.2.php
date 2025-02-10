<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Core_Model_Resource_Setup $installer
 */
$installer = $this;

$installer->run("
    ALTER TABLE {$this->getTable('catalogsearch_query')} ADD `display_in_terms` TINYINT( 1 ) NOT NULL DEFAULT '0';
");
