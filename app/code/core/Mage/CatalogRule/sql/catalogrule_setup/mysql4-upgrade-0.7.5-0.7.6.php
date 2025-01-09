<?php

/**
 * @category   Mage
 * @package    Mage_CatalogRule
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;

$installer->getConnection()->addKey(
    $installer->getTable('catalogrule_product'),
    'sort_order',
    ['rule_id', 'from_time','to_time','website_id','customer_group_id','product_id','sort_order'],
    'unique',
);
