<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Reports
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->modifyColumn(
    $installer->getTable('reports/compared_product_index'),
    'visitor_id',
    'INT(10) UNSIGNED NULL',
);

$installer->endSetup();
