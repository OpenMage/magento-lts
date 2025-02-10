<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Core_Model_Resource_Setup $this
 */
$installer = $this;
$installer->startSetup();

$installer->run("DROP TABLE IF EXISTS {$this->getTable('tax_class_group')};");

$installer->endSetup();
