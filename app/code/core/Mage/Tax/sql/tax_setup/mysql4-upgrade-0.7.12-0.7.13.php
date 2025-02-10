<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Core_Model_Resource_Setup $installer
 */
$installer = $this;
$installer->startSetup();

$installer->run("
    ALTER TABLE {$this->getTable('tax_calculation_rate')} CHANGE `tax_postcode` `tax_postcode` VARCHAR(21) NOT NULL;
");

$installer->endSetup();
