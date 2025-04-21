<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/** @var Mage_Catalog_Model_Resource_Setup  $installer */
$installer = $this;
$installer->startSetup();

$installer->run("
    ALTER TABLE `{$installer->getTable('catalog_product_entity')}`
        CHANGE `type_id` `type_id` VARCHAR(32) DEFAULT 'simple' NOT NULL;
    UPDATE `{$installer->getTable('catalog_product_entity')}`
        SET `type_id` = CASE `type_id`
            WHEN '1' THEN 'simple'
            WHEN '2' THEN 'bundle'
            WHEN '3' THEN 'configurable'
            WHEN '4' THEN 'grouped'
            WHEN '5' THEN 'virtual'
            ELSE `type_id` END;
");

$installer->endSetup();
