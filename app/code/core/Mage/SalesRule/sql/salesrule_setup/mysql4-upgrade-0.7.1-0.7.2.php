<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Core_Model_Resource_Setup $installer
 */
$installer = $this;
$installer->startSetup();

$installer->run("
ALTER TABLE `{$this->getTable('salesrule')}`
    ADD COLUMN `times_used` int (11) unsigned DEFAULT '0' NOT NULL
        AFTER `simple_free_shipping`;
");

$installer->endSetup();
