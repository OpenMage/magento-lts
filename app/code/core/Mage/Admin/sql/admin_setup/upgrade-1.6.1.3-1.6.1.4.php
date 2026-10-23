<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Admin
 */

/** @var Mage_Core_Model_Resource_Setup $this */
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
    'admin/xmlconnect/templates',
];

$installer->getConnection()->delete(
    $installer->getTable('admin/rule'),
    ['resource_id IN (?)' => $obsoleteAcl],
);

$installer->endSetup();
