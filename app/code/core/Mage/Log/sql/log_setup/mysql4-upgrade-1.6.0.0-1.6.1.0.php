<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Log
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->dropIndex($installer->getTable('log/url_table'), 'PRIMARY');
$installer->getConnection()->addIndex($installer->getTable('log/url_table'), 'url_id', ['url_id']);

$installer->endSetup();
