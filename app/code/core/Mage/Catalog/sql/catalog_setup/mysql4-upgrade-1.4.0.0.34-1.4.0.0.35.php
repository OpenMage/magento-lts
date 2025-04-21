<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/** @var Mage_Catalog_Model_Resource_Setup  $installer */
$installer = $this;

$entityTypeId = $installer->getEntityTypeId('catalog_product');
$installer->run("
    UPDATE `{$installer->getTable('eav/attribute')}`
    SET `source_model` = 'eav/entity_attribute_source_boolean'
    WHERE `entity_type_id` = {$entityTypeId} AND `frontend_input` = 'boolean'");
