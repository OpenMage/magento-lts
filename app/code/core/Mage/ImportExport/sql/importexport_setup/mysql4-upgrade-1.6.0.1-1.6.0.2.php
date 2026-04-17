<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_ImportExport
 */

/** @var Mage_ImportExport_Model_Resource_Setup $this */
$installer = $this;

$installer->getConnection()->modifyColumn(
    $installer->getTable('importexport/importdata'),
    'data',
    [
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => '4G',
        'default' => '',
        'comment' => 'Data',
    ],
);
