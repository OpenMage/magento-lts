<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @license Open Software License (OSL 3.0)
 * @var Mage_Core_Model_Resource_Setup $installer
 */
$installer = $this;


$installer->run("
    ALTER TABLE {$this->getTable('catalogsearch_query')} CHANGE `display_in_terms` `display_in_terms` TINYINT( 1 ) NOT NULL DEFAULT '1';
");
