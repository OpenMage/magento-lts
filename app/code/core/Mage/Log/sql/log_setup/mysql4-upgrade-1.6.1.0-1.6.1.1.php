<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Log
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->changeColumn(
    $installer->getTable('log/visitor_info'),
    'server_addr',
    'server_addr',
    'varbinary(16)'
);

$installer->getConnection()->update(
    $installer->getTable('log/visitor_info'),
    [
         'server_addr' => new Zend_Db_Expr('UNHEX(HEX(CAST(server_addr as UNSIGNED INT)))')
    ]
);

$installer->getConnection()->changeColumn(
    $installer->getTable('log/visitor_info'),
    'remote_addr',
    'remote_addr',
    'varbinary(16)'
);

$installer->getConnection()->update(
    $installer->getTable('log/visitor_info'),
    [
         'remote_addr' => new Zend_Db_Expr('UNHEX(HEX(CAST(remote_addr as UNSIGNED INT)))')
    ]
);

$installer->getConnection()->changeColumn(
    $installer->getTable('log/visitor_online'),
    'remote_addr',
    'remote_addr',
    'varbinary(16)'
);

$installer->getConnection()->update(
    $installer->getTable('log/visitor_online'),
    [
         'remote_addr' => new Zend_Db_Expr('UNHEX(HEX(CAST(remote_addr as UNSIGNED INT)))')
    ]
);

$installer->endSetup();
