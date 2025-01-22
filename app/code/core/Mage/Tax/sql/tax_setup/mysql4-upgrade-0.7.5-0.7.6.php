<?php

/**
 * @category   Mage
 * @package    Mage_Tax
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
