<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Catalog
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
