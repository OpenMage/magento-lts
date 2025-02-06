<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Catalog
 */

/** @var Mage_Catalog_Model_Resource_Setup  $installer */
$installer = $this;

$entityTypeId = (int) $installer->getEntityTypeId('catalog_product');
$installer->run("
    UPDATE `{$installer->getTable('eav_attribute')}`
        SET `apply_to` = IF(`use_in_super_product`, 'simple,grouped,configurable', 'simple')
        WHERE `entity_type_id` = $entityTypeId;
");
$installer->getConnection()->dropColumn($installer->getTable('eav_attribute'), 'use_in_super_product');
