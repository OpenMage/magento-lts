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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Shipping
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Drop indexes
 */
$installer->getConnection()->dropIndex(
    $installer->getTable('shipping/tablerate'),
    'DEST_COUNTRY'
);


/**
 * Change columns
 */
$tables = array(
    $installer->getTable('shipping/tablerate') => array(
        'columns' => array(
            'pk' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Primary key'
            ),
            'website_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Website Id'
            ),
            'dest_country_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 4,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Destination coutry ISO/2 or ISO/3 code'
            ),
            'dest_region_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Destination Region Id'
            ),
            'dest_zip' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 10,
                'nullable'  => false,
                'default'   => '*',
                'comment'   => 'Destination Post Code (Zip)'
            ),
            'condition_name' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 20,
                'nullable'  => false,
                'comment'   => 'Rate Condition name'
            ),
            'condition_value' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Rate condition value'
            ),
            'price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Price'
            ),
            'cost' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Cost'
            )
        ),
        'comment' => 'Shipping Tablerate'
    )
);

$installer->getConnection()->modifyTables($tables);


/**
 * Add indexes
 */
$installer->getConnection()->addIndex(
    $installer->getTable('shipping/tablerate'),
    $installer->getIdxName(
        'shipping/tablerate',
        array('website_id', 'dest_country_id', 'dest_region_id', 'dest_zip', 'condition_name', 'condition_value'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('website_id', 'dest_country_id', 'dest_region_id', 'dest_zip', 'condition_name', 'condition_value'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->endSetup();
