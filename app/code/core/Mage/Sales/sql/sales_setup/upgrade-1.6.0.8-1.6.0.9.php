<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Sales
 */

/** @var Mage_Sales_Model_Entity_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->changeColumn(
    $installer->getTable('sales/quote'),
    'remote_ip',
    'remote_ip',
    'VARCHAR(255) default NULL COMMENT \'Remote Ip\'',
);

$installer->endSetup();
