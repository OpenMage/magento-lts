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
 * @package     Mage_GiftMessage
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_GiftMessage_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Change columns
 */
$tables = array(
    $installer->getTable('giftmessage/message') => array(
        'columns' => array(
            'gift_message_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'GiftMessage Id'
            ),
            'customer_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Customer id'
            ),
            'sender' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Sender'
            ),
            'recipient' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Recipient'
            ),
            'message' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'comment'   => 'Message'
            )
        ),
        'comment' => 'Gift Message'
    ),
    $installer->getTable('sales/quote') => array(
        'columns' => array(
            'gift_message_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Gift Message Id'
            )
        )
    ),
    $installer->getTable('sales/quote_address') => array(
        'columns' => array(
            'gift_message_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Gift Message Id'
            )
        )
    ),
    $installer->getTable('sales/quote_item') => array(
        'columns' => array(
            'gift_message_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Gift Message Id'
            )
        )
    ),
    $installer->getTable('sales/quote_address_item') => array(
        'columns' => array(
            'gift_message_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Gift Message Id'
            )
        )
    ),
    $installer->getTable('sales/order') => array(
        'columns' => array(
            'gift_message_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Gift Message Id'
            )
        )
    ),
    $installer->getTable('sales/order_item') => array(
        'columns' => array(
            'gift_message_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Gift Message Id'
            ),
            'gift_message_available' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Gift Message Available'
            )
        )
    )
);

$installer->getConnection()->modifyTables($tables);

$installer->endSetup();
