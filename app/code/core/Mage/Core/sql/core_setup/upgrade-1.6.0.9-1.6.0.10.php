<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/** @var Mage_Core_Model_Resource_Setup $this */
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
