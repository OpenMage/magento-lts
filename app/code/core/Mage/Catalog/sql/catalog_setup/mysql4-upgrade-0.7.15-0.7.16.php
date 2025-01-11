<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
