<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Core_Model_Resource_Setup $installer
 */
$installer = $this;

$installer->run("
    ALTER TABLE {$this->getTable('catalogsearch_query')} ADD `updated_at` DATETIME NOT NULL;
");
