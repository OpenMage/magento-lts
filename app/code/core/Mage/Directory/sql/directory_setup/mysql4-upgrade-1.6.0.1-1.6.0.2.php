<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Directory
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

/** @var Varien_Db_Adapter_Pdo_Mysql $connection */
$connection  = $installer->getConnection();

$regionTable = $installer->getTable('directory/country_region');

/* Armed Forces changes based on USPS */

/* Armed Forces Middle East (AM) is now served by Armed Forces Europe (AE) */
$bind = ['code' => 'AE'];
$where = ['code = ?' => 'AM'];

$connection->update($regionTable, $bind, $where);

/* Armed Forces Canada (AC) is now served by Armed Forces Europe (AE) */
$bind = ['code' => 'AE'];
$where = ['code = ?' => 'AC'];

$connection->update($regionTable, $bind, $where);

/* Armed Forces Africa (AF) is now served by Armed Forces Europe (AE) */
$bind = ['code' => 'AE'];
$where = ['code = ?' => 'AF'];

$connection->update($regionTable, $bind, $where);

$installer->endSetup();
