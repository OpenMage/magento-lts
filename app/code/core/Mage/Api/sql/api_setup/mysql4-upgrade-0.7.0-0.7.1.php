<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->run("ALTER TABLE {$this->getTable('api_user')} ADD `sessid` VARCHAR(40) NOT NULL AFTER `lognum`;");

$installer->endSetup();
