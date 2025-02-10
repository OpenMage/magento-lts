<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Core_Model_Resource_Setup $installer
 */
$installer = $this;
$installer->startSetup();

$installer->run("

ALTER TABLE `{$this->getTable('design_change')}`
 CHANGE `date_from` `date_from` DATE NULL,
 CHANGE `date_to` `date_to` DATE NULL

");

$installer->endSetup();
