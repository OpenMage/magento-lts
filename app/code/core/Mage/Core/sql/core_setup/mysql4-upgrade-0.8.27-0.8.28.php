<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
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
