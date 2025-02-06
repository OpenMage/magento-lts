<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Downloadable
 */

/** @var Mage_Catalog_Model_Resource_Setup  $installer */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addKey(
    $installer->getTable('downloadable/link_title'),
    'UNQ_LINK_TITLE_STORE',
    ['link_id', 'store_id'],
    'unique',
);
$installer->getConnection()->addKey(
    $installer->getTable('downloadable/sample_title'),
    'UNQ_SAMPLE_TITLE_STORE',
    ['sample_id', 'store_id'],
    'unique',
);

$installer->endSetup();
