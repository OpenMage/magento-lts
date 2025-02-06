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

$entityTypeId = $installer->getEntityTypeId('catalog_product');
$installer->run("
    UPDATE `{$installer->getTable('eav/attribute')}`
    SET `source_model` = 'eav/entity_attribute_source_boolean'
    WHERE `entity_type_id` = {$entityTypeId} AND `frontend_input` = 'boolean'");
