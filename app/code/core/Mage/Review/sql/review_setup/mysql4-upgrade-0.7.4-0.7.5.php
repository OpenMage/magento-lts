<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Core_Model_Resource_Setup $installer
 */
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
