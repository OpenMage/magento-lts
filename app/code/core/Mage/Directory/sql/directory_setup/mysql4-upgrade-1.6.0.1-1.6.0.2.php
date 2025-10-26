<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Directory
 */

/** @var Mage_Core_Model_Resource_Setup $this */
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
