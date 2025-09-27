<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addConstraint(
    'FK_CORE_URL_REWRITE_STORE',
    $installer->getTable('core/url_rewrite'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
    'CASCADE',
    'CASCADE',
    true,
);

$installer->endSetup();
