<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Sales_Model_Entity_Setup $installer */
$installer = $this;

$billingAgreementTable = $installer->getTable('sales/billing_agreement');

$installer->getConnection()->addColumn(
    $billingAgreementTable,
    'store_id',
    'smallint(5) unsigned DEFAULT NULL'
);

$installer->getConnection()->addConstraint(
    'FK_BILLING_AGREEMENT_STORE',
    $billingAgreementTable,
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
    'SET NULL',
    'CASCADE'
);
