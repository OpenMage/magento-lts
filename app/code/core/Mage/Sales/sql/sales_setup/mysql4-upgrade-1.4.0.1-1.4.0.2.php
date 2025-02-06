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

$installer->getConnection()->addKey($installer->getTable('sales_flat_order'), 'IDX_UPDATED_AT', 'updated_at');
$installer->getConnection()->addKey($installer->getTable('sales_flat_shipment'), 'IDX_CREATED_AT', 'created_at');
$installer->getConnection()->addKey($installer->getTable('sales_flat_shipment'), 'IDX_UPDATED_AT', 'updated_at');
