<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Admin
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

//Increase password field length
$installer->getConnection()->changeColumn($installer->getTable('admin/user'), 'password', 'password', [
    'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length'    => 100,
    'comment'   => 'User Password',
]);

$installer->endSetup();
