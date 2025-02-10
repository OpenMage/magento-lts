<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Core_Model_Resource_Setup $installer
 */
$installer = $this;
$installer->startSetup();
$installer->getConnection()->addColumn($installer->getTable('core/email_template'), 'orig_template_code', 'VARCHAR(200) DEFAULT NULL');
$installer->endSetup();
