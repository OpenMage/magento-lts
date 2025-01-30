<?php

/**
 * @category   Mage
 * @package    Mage_ImportExport
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
