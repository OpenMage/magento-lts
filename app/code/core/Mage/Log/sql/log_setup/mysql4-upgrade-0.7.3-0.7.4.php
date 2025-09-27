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

$installer->getConnection()->addKey($installer->getTable('log/customer'), 'IDX_VISITOR', 'visitor_id');
$installer->getConnection()->addKey($installer->getTable('log/url_table'), 'PRIMARY', 'url_id', 'primary');
$installer->getConnection()->addKey($installer->getTable('log/url_table'), 'IDX_VISITOR', 'visitor_id');

$installer->endSetup();
