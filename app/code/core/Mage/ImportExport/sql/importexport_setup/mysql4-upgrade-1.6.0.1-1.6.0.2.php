<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_ImportExport
 */

/** @var Mage_ImportExport_Model_Resource_Setup $installer */
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
