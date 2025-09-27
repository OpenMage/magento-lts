<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;

$installer->getConnection()->addColumn($installer->getTable('core/resource'), 'data_version', 'varchar(50)');

/*
 * Update core_resource table to prevent running data upgrade install scripts,
 * New 'data_version' column will contain value from 'version' column
 */
$installer->getConnection()->update(
    $this->getTable('core/resource'),
    ['data_version' => new Zend_Db_Expr('version')],
);
