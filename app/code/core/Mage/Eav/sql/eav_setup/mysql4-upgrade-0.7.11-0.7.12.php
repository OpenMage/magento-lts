<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Eav_Model_Entity_Setup $installer
 */
$installer = $this;
$installer->startSetup();
$installer->getConnection()->addColumn($installer->getTable('eav_attribute'), 'is_html_allowed_on_front', "tinyint(1) unsigned not null default '0' after `is_visible_on_front`");
$installer->endSetup();
