<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Admin
 * @copyright  Copyright (c) 2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
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
