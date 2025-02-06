<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Tax
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$conn = $installer->getConnection();
/** @var Varien_Db_Adapter_Pdo_Mysql $conn */
$conn->addColumn($installer->getTable('tax_rate'), 'tax_country_id', "char(2) not null default 'US' after `tax_rate_id`");

$installer->endSetup();
