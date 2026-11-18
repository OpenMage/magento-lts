<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Admin
 */

/** @var Mage_Core_Model_Resource_Setup $this */
$installer = $this;
$installer->startSetup();

// Every session of the user rebuilds its ACL when this changes, see Mage_Admin_Model_Resource_User::bumpAclVersion()
$installer->getConnection()->addColumn($installer->getTable('admin/user'), 'acl_version', [
    'type'     => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'unsigned' => true,
    'nullable' => false,
    'default'  => '0',
    'comment'  => 'ACL Version',
]);

$installer->endSetup();
