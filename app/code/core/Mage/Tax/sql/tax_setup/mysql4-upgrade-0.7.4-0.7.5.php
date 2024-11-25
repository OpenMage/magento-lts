<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Tax
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Customer_Model_Entity_Setup $installer */
$installer = $this;
$installer->startSetup();

$customerTaxClassIds = $installer->getConnection()->fetchCol(
    "SELECT class_id FROM {$installer->getTable('tax_class')}
        WHERE class_type = 'CUSTOMER'
        ORDER BY class_id ASC",
);

if (count($customerTaxClassIds) > 0) {
    $installer->run(
        "UPDATE {$installer->getTable('customer_group')}
            SET tax_class_id = {$customerTaxClassIds[0]}
            WHERE tax_class_id NOT IN (" . implode(',', $customerTaxClassIds) . ')',
    );
}

$installer->endSetup();
