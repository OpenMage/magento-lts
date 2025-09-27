<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Tax
 */

/** @var Mage_Customer_Model_Entity_Setup $installer */
$installer  = $this;
$installer->startSetup();

$table = $installer->getTable('tax_rule');
$installer->run("
ALTER TABLE `{$table}` ADD `priority` SMALLINT( 5 ) NOT NULL;
ALTER TABLE `{$table}` ADD INDEX `IDX_PRIORITY` (`priority`);
");

$installer->endSetup();
