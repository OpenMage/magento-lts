<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
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
