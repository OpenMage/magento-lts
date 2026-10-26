<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Admin
 */

/** @var Mage_Oauth_Model_Resource_Setup $this */
$installer = $this;
$installer->startSetup();

$table = $installer->getTable('admin/rule');
$resourceIds = [
    'admin/system/api/consumer' => 'admin/system/api/oauth_consumer',
    'admin/system/api/consumer/delete' => 'admin/system/api/oauth_consumer/delete',
    'admin/system/api/consumer/edit' => 'admin/system/api/oauth_consumer/edit',
    'admin/system/api/authorizedTokens' => 'admin/system/api/oauth_authorized_tokens',
];

foreach ($resourceIds as $oldId => $newId) {
    $installer->getConnection()->update(
        $table,
        ['resource_id' => $newId],
        ['resource_id = ?' => $oldId],
    );
}

$installer->endSetup();
