<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Bundle
 */

/** @var Mage_Catalog_Model_Resource_Setup  $installer */
$installer = $this;
$installer->startSetup();
$installer->getConnection()->addKey(
    $installer->getTable('bundle/option_value'),
    'UNQ_OPTION_STORE',
    ['option_id', 'store_id'],
    'unique',
);
$installer->endSetup();
