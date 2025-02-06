<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_CatalogRule
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;

$installer->getConnection()->addKey(
    $installer->getTable('catalogrule_product'),
    'sort_order',
    ['rule_id', 'from_time','to_time','website_id','customer_group_id','product_id','sort_order'],
    'unique',
);
