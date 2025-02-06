<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Tax
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->run("
    ALTER TABLE {$this->getTable('tax_rate_data')} DROP FOREIGN KEY `FK_TAX_RATE_DATA_TAX_RATE`;
    ALTER TABLE {$this->getTable('tax_rate')} CHANGE `tax_rate_id` `tax_rate_id` INT UNSIGNED NOT NULL AUTO_INCREMENT;
    ALTER TABLE {$this->getTable('tax_rate_data')} CHANGE `tax_rate_data_id` `tax_rate_data_id` INT UNSIGNED NOT NULL AUTO_INCREMENT;
    ALTER TABLE {$this->getTable('tax_rate_data')} CHANGE `tax_rate_id` `tax_rate_id` INT UNSIGNED NOT NULL DEFAULT '0';
    ALTER TABLE {$this->getTable('tax_rate_data')} ADD CONSTRAINT `FK_TAX_RATE_DATA_TAX_RATE` FOREIGN KEY (`tax_rate_id`) REFERENCES {$this->getTable('tax_rate')} (`tax_rate_id`) ON DELETE CASCADE ON UPDATE CASCADE;
");

$installer->endSetup();
