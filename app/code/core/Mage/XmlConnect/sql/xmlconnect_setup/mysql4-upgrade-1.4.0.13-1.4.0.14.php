<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Xmlconnect Config data upgrade
 *
 * @category    Mage
 * @package     Mage_Xmlconnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Create table 'xmlconnect_config_data'
 */
$configTableName = $installer->getTable('xmlconnect/configData');
$configTable = $installer->getConnection()
    ->newTable($configTableName)
    ->addColumn('application_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned'  => true,
            'nullable'  => false,
        ), 'Application Id')
    ->addColumn('category', Varien_Db_Ddl_Table::TYPE_TEXT, 60, array(
            'nullable'  => false,
            'default'  => 'default',
        ), 'Category')
    ->addColumn('path', Varien_Db_Ddl_Table::TYPE_TEXT, 250, array(
            'nullable'  => false,
        ), 'Path')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
            'nullable'  => false,
        ), 'Value')
    ->addIndex(
        $installer->getIdxName(
            $configTableName,
            array('application_id', 'category', 'path'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('application_id', 'category', 'path'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
    )
    ->addForeignKey(
        $installer->getFkName(
            $configTableName,
            'application_id',
            $installer->getTable('xmlconnect/application'),
            'application_id'
        ),
        'application_id',
        $installer->getTable('xmlconnect/application'),
        'application_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Xmlconnect Configuration Data');
$installer->getConnection()->createTable($configTable);

$installer->endSetup();
