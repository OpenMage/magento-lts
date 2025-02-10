<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Eav_Model_Entity_Setup $installer
 */
$installer  = $this;
$installer->startSetup();
$installer->getConnection()->addColumn($installer->getTable('eav/attribute'), 'is_used_for_price_rules', "TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '1'");
$installer->endSetup();
