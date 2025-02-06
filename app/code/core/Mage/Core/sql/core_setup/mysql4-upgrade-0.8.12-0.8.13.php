<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
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
