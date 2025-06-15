<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Eav
 */

/** @var Mage_Eav_Model_Entity_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->dropColumn($this->getTable('eav_attribute'), 'attribute_name');
$installer->getConnection()->dropColumn($this->getTable('eav_attribute'), 'apply_to');
$installer->run("
    ALTER TABLE {$this->getTable('eav_attribute')} ADD COLUMN `is_configurable` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1;
");

$installer->endSetup();
