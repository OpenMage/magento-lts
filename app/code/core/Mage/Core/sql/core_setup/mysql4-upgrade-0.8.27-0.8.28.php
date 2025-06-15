<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;

$tagsTableName = $installer->getTable('core/cache_tag');
$installer->getConnection()->truncate($tagsTableName);
$installer->getConnection()->modifyColumn($tagsTableName, 'tag', 'VARCHAR(100)');
$installer->getConnection()->modifyColumn($tagsTableName, 'cache_id', 'VARCHAR(200)');
$installer->getConnection()->addKey($tagsTableName, '', ['tag', 'cache_id'], 'PRIMARY');
$installer->getConnection()->dropKey($tagsTableName, 'IDX_TAG');
