<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;

$tagsTableName = $installer->getTable('core/cache_tag');
$installer->getConnection()->truncate($tagsTableName);
$installer->getConnection()->modifyColumn($tagsTableName, 'tag', 'VARCHAR(100)');
$installer->getConnection()->modifyColumn($tagsTableName, 'cache_id', 'VARCHAR(200)');
$installer->getConnection()->addKey($tagsTableName, '', ['tag', 'cache_id'], 'PRIMARY');
$installer->getConnection()->dropKey($tagsTableName, 'IDX_TAG');
