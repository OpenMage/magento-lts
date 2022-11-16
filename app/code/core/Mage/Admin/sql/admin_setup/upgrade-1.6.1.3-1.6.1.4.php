<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Admin
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$obsoleteAcl = [
    'admin/page_cache',
    'admin/system/config/moneybookers',
    'admin/system/extensions',
    'admin/system/extensions/local',
    'admin/system/extensions/custom',
    'admin/system/tools/backup',
    'admin/system/tools/backup/rollback',
    'admin/system/tools/compiler',
    'admin/xmlconnect',
    'admin/xmlconnect/mobile',
    'admin/xmlconnect/admin_connect',
    'admin/xmlconnect/queue',
    'admin/xmlconnect/history',
    'admin/xmlconnect/templates'
];

$installer->getConnection()->delete(
    $installer->getTable('admin/rule'),
    ['resource_id IN (?)' => $obsoleteAcl]
);

$installer->endSetup();
