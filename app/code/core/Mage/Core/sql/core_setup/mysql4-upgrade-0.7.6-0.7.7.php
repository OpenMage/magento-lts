<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer  = $this;
$installer->startSetup();

$installer->run("
UPDATE `{$this->getTable('core_store')}` SET `code` = 'admin', `name` = 'Admin' WHERE `code` LIKE 'default';
UPDATE `{$this->getTable('core_store')}` SET `code` = 'default', `name` = 'Default Store View' WHERE `code` LIKE 'base';
    ");

$installer->endSetup();
