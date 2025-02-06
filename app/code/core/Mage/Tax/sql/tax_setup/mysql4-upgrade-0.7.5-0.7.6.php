<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
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
