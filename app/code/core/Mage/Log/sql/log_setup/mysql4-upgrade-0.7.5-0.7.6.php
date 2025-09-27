<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Log
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer  = $this;
$installer->startSetup();
$installer->getConnection()->modifyColumn($installer->getTable('log/visitor_online'), 'remote_addr', 'bigint(20) NOT NULL');
$installer->endSetup();
