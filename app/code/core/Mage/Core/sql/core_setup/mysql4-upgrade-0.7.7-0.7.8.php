<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer  = $this;
$installer->startSetup();

$table = $this->getTable('core_translate');
$conn = $installer->getConnection();

$conn->addColumn($table, 'locale', "varchar(20) not null default 'en_US'");
$conn->dropKey($table, 'IDX_CODE');
$conn->raw_query('alter table `' . $table . '` add unique key `IDX_CODE` (`store_id`, `locale`, `string`)');

$installer->endSetup();
