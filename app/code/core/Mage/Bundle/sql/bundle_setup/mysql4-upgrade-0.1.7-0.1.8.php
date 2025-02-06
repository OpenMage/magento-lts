<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
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
