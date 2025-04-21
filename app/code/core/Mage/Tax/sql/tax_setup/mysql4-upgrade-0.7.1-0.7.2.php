<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Tax
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$conn = $installer->getConnection();
/** @var Varien_Db_Adapter_Pdo_Mysql $conn */
$conn->addColumn($installer->getTable('tax_rate'), 'tax_country_id', "char(2) not null default 'US' after `tax_rate_id`");

$installer->endSetup();
