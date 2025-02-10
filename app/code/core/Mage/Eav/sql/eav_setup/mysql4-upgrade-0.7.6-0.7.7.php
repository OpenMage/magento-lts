<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Eav_Model_Entity_Setup $installer
 */
$installer  = $this;
$installer->startSetup();

$installer->run("
ALTER TABLE {$installer->getTable('eav_attribute_group')}
    CHANGE `default_id` `default_id` smallint unsigned NULL default '0';
");

$installer->endSetup();
