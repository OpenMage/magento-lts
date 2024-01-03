<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$table = $installer->getTable('core/config_data');

if ($installer->getConnection()->isTableExists($table)) {
    $oldPath = 'admin/security/crate_admin_user_notification';
    $newPath = 'admin/security/create_admin_user_notification';

    $select = $installer->getConnection()->select()
        ->from($table, ['config_id', 'path'])
        ->where('path = ?', $oldPath);

    $rows = $installer->getConnection()->fetchAll($select);

    foreach ($rows as $row) {
        $installer->getConnection()->update(
            $table,
            ['path' => $newPath],
            ['config_id = ?' => $row['config_id']]
        );
    }
}

$installer->endSetup();
