<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Eav
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$connection = $installer->getConnection();

$connection->delete(
    $this->getTable('eav/attribute'),
    $connection->prepareSqlCondition('attribute_code', 'enable_googlecheckout'),
);

$installer->endSetup();
