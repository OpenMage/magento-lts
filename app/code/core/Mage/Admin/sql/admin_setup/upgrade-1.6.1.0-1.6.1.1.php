<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Core_Model_Resource_Setup $installer
 */
$installer = $this;
$installer->startSetup();

//Increase password field length
$installer->getConnection()->changeColumn($installer->getTable('admin/user'), 'password', 'password', [
    'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length'    => 100,
    'comment'   => 'User Password',
]);

$installer->endSetup();
