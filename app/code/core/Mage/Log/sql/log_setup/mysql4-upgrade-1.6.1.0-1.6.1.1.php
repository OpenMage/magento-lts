<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Log
 */

/** @var Mage_Core_Model_Resource_Setup $this */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->changeColumn(
    $installer->getTable('log/visitor_info'),
    'server_addr',
    'server_addr',
    'varbinary(16)',
);

$installer->getConnection()->update(
    $installer->getTable('log/visitor_info'),
    [
        'server_addr' => new Zend_Db_Expr('UNHEX(HEX(CAST(server_addr as UNSIGNED INT)))'),
    ],
);

$installer->getConnection()->changeColumn(
    $installer->getTable('log/visitor_info'),
    'remote_addr',
    'remote_addr',
    'varbinary(16)',
);

$installer->getConnection()->update(
    $installer->getTable('log/visitor_info'),
    [
        'remote_addr' => new Zend_Db_Expr('UNHEX(HEX(CAST(remote_addr as UNSIGNED INT)))'),
    ],
);

$installer->getConnection()->changeColumn(
    $installer->getTable('log/visitor_online'),
    'remote_addr',
    'remote_addr',
    'varbinary(16)',
);

$installer->getConnection()->update(
    $installer->getTable('log/visitor_online'),
    [
        'remote_addr' => new Zend_Db_Expr('UNHEX(HEX(CAST(remote_addr as UNSIGNED INT)))'),
    ],
);

$installer->endSetup();
