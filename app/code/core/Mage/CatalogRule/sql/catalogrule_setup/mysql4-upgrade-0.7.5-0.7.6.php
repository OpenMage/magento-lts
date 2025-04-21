<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
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
