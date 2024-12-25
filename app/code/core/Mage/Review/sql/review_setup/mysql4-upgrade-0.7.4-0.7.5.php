<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Review
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$tableReviewDetail = $installer->getTable('review/review_detail');
$tableCustomer = $installer->getTable('customer_entity');

$installer->run("UPDATE {$tableReviewDetail} SET customer_id=NULL WHERE customer_id NOT IN (SELECT entity_id FROM {$tableCustomer})");

$installer->getConnection()->addConstraint(
    'FK_REVIEW_DETAIL_CUSTOMER',
    $tableReviewDetail,
    'customer_id',
    $tableCustomer,
    'entity_id',
    'SET NULL',
    'CASCADE',
    true,
);

$installer->endSetup();
