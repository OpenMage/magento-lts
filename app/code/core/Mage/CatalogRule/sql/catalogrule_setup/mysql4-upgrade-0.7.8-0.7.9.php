<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Core_Model_Resource_Setup $installer
 */
$installer = $this;
$connection = $installer->getConnection();
$connection->addKey($installer->getTable('catalogrule/rule_product'), 'IDX_FROM_TIME', 'from_time');
$connection->addKey($installer->getTable('catalogrule/rule_product'), 'IDX_TO_TIME', 'to_time');
