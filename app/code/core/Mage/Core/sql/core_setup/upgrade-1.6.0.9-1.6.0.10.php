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
$installer->startSetup();

$table = $installer->getTable('core/file_storage');

/**
 * Change column
 */
if ($installer->getConnection()->isTableExists($table)) {
    $installer->getConnection()->modifyColumn(
        $table,
        'filename',
        [
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length' => 255,
            'nullable' => false,
            'comment' => 'Filename',
        ],
    );
}

$installer->endSetup();
