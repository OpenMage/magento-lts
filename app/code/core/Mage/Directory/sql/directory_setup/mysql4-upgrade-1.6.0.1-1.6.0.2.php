<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Directory
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
