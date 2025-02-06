<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Eav
 */

/** @var Mage_Eav_Model_Entity_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addConstraint(
    'FK_EAV_ENTITY_ATTRIBUTE_ATTRIBUTE',
    $installer->getTable('eav/entity_attribute'),
    'attribute_id',
    $installer->getTable('eav/attribute'),
    'attribute_id',
    'CASCADE',
    'CASCADE',
    true,
);

$installer->endSetup();
