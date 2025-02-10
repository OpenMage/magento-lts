<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Eav_Model_Entity_Setup $installer
 */
$installer = $this;
$installer->startSetup();
$installer->getConnection()->addColumn($installer->getTable('eav_attribute'), 'apply_to', 'VARCHAR(255) NOT NULL');
$installer->endSetup();
