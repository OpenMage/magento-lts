<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Sales_Model_Entity_Setup $installer */
$installer = $this;

$installer->getConnection()->addColumn($installer->getTable('sales_flat_order_grid'), 'updated_at', 'datetime default NULL');
$installer->getConnection()->addKey($installer->getTable('sales_flat_order_grid'), 'IDX_UPDATED_AT', 'updated_at');
$installer->run("
    UPDATE {$installer->getTable('sales_flat_order_grid')} AS g
        JOIN {$installer->getTable('sales_flat_order')} AS o ON g.entity_id=o.entity_id
        SET g.updated_at=o.updated_at
");
