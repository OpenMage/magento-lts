<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Core
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$connection = $installer->getConnection();
$connection->addColumn($installer->getTable('core_config_data'), 'updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP);

$installer->endSetup();
