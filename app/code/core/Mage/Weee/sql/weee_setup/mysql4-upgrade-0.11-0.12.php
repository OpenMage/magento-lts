<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Weee
 */

/** @var Mage_Weee_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('weee_tax'), 'entity_type_id', 'smallint (5) unsigned not null');
$installer->run("
UPDATE
    `{$installer->getTable('weee_tax')}`
SET
    `entity_type_id` = (
        SELECT
            `entity_type_id`
        FROM
            `{$installer->getTable('eav_entity_type')}`
        WHERE
            `entity_type_code` = 'catalog_product'
    );
");

$installer->endSetup();
