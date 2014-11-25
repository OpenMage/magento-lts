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
 * @copyright  Copyright (c) 2006-2014 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var $installer Mage_XmlConnect_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Application table 'xmlconnect_application'
 */
$appTableName = $installer->getTable('xmlconnect/application');

/**
 * Create table 'xmlconnect_images'
 */
$imagesTableName = $installer->getTable('xmlconnect/images');
$imagesTable = $installer->getConnection()
    ->newTable($imagesTableName)
    ->addColumn('image_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
        ), 'Image Id')
    ->addColumn('application_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned'  => true,
            'nullable'  => false,
        ), 'Application Id')
    ->addColumn('image_file', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'nullable'  => false,
        ), 'Image File')
    ->addColumn('image_type', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'nullable'  => false,
        ), 'Image Type')
    ->addColumn('order', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
            'nullable'  => false,
        ), 'Order')
    ->addForeignKey(
        $installer->getFkName($imagesTableName, 'application_id', $appTableName, 'application_id'),
        'application_id',
        $appTableName,
        'application_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Xmlconnect Images');
$installer->getConnection()->createTable($imagesTable);

$installer->endSetup();
