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
 * @package     Mage_Checkout
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Drop foreign keys
 */
$installer->getConnection()->dropForeignKey(
    $installer->getTable('checkout/agreement_store'),
    'FK_CHECKOUT_AGREEMENT'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('checkout/agreement_store'),
    'FK_CHECKOUT_AGREEMENT_STORE'
);


/**
 * Drop indexes
 */
$installer->getConnection()->dropIndex(
    $installer->getTable('checkout/agreement_store'),
    'AGREEMENT_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('checkout/agreement_store'),
    'FK_CHECKOUT_AGREEMENT_STORE'
);


/*
 * Change columns
 */
$tables = array(
    $installer->getTable('checkout/agreement') => array(
        'columns' => array(
            'agreement_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Agreement Id'
            ),
            'name' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Name'
            ),
            'content' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Content'
            ),
            'content_height' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 25,
                'comment'   => 'Content Height'
            ),
            'checkbox_text' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Checkbox Text'
            ),
            'is_active' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Is Active'
            ),
            'is_html' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Is Html'
            )
        ),
        'comment' => 'Checkout Agreement'
    ),
    $installer->getTable('checkout/agreement_store') => array(
        'columns' => array(
            'agreement_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Agreement Id'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Store Id'
            )
        ),
        'comment' => 'Checkout Agreement Store'
    )
);

$installer->getConnection()->modifyTables($tables);


/**
 * Add indexes
 */
$installer->getConnection()->addIndex(
    $installer->getTable('checkout/agreement_store'),
    'PRIMARY',
    array('agreement_id','store_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY
);


/**
 * Add foreign keys
 */
$installer->getConnection()->addForeignKey(
    $installer->getFkName('checkout/agreement_store', 'agreement_id', 'checkout/agreement', 'agreement_id'),
    $installer->getTable('checkout/agreement_store'),
    'agreement_id',
    $installer->getTable('checkout/agreement'),
    'agreement_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('checkout/agreement_store', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('checkout/agreement_store'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->endSetup();
