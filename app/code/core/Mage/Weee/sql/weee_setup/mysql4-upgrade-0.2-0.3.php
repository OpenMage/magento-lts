<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Weee_Model_Resource_Setup $installer
 */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('weee_tax'), 'attribute_id', 'smallint (5) unsigned not null');

$installer->endSetup();
