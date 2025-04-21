<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Review
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addConstraint(
    'FK_REVIEW_STORE_REVIEW',
    $installer->getTable('review/review_store'),
    'review_id',
    $installer->getTable('review/review'),
    'review_id',
    'CASCADE',
    'CASCADE',
    true,
);

$installer->endSetup();
