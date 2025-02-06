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

$billingAgreementTable = $installer->getTable('sales/billing_agreement');

$installer->getConnection()->addColumn(
    $billingAgreementTable,
    'agreement_label',
    'varchar(255)',
);
