<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Eav_Model_Entity_Setup $installer
 */
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
