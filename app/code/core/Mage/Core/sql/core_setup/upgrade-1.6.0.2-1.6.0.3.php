<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$connection = $installer->getConnection();
$table = $installer->getTable('core/translate');

$connection->dropIndex($table, $installer->getIdxName(
    'core/translate',
    ['store_id', 'locale', 'string'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
));

$connection->addColumn($table, 'crc_string', [
    'type'     => Varien_Db_Ddl_Table::TYPE_BIGINT,
    'nullable' => false,
    'default'  => crc32(Mage_Core_Model_Translate::DEFAULT_STRING),
    'comment'  => 'Translation String CRC32 Hash',
]);

$connection->addIndex($table, $installer->getIdxName(
    'core/translate',
    ['store_id', 'locale', 'crc_string', 'string'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
), ['store_id', 'locale', 'crc_string', 'string'], Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE);

$installer->endSetup();
