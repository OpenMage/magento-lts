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

/** @var Mage_Catalog_Model_Resource_Setup  $this */
$this->startSetup();

// get options_container attribute and update its value to 'container1' for configurable products
$attribute = $this->getAttribute('catalog_product', 'options_container');
if (!empty($attribute['attribute_id'])) {
    $this->run("
        UPDATE {$this->getTable('catalog_product_entity_varchar')}
        SET value = 'container1'
        WHERE
            entity_id IN (SELECT entity_id FROM {$this->getTable('catalog_product_entity')} WHERE type_id='configurable')
            AND attribute_id={$attribute['attribute_id']}
    ");
}

$this->endSetup();
