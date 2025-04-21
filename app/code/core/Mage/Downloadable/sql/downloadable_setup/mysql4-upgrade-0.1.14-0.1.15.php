<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
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
