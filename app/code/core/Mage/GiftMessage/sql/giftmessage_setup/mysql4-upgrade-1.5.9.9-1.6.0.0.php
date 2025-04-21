<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_GiftMessage
 */

/** @var Mage_GiftMessage_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

/**
 * Change columns
 */
$tables = [
    $installer->getTable('giftmessage/message') => [
        'columns' => [
            'gift_message_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'GiftMessage Id',
            ],
            'customer_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Customer id',
            ],
            'sender' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Sender',
            ],
            'recipient' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Recipient',
            ],
            'message' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'comment'   => 'Message',
            ],
        ],
        'comment' => 'Gift Message',
    ],
    $installer->getTable('sales/quote') => [
        'columns' => [
            'gift_message_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Gift Message Id',
            ],
        ],
    ],
    $installer->getTable('sales/quote_address') => [
        'columns' => [
            'gift_message_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Gift Message Id',
            ],
        ],
    ],
    $installer->getTable('sales/quote_item') => [
        'columns' => [
            'gift_message_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Gift Message Id',
            ],
        ],
    ],
    $installer->getTable('sales/quote_address_item') => [
        'columns' => [
            'gift_message_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Gift Message Id',
            ],
        ],
    ],
    $installer->getTable('sales/order') => [
        'columns' => [
            'gift_message_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Gift Message Id',
            ],
        ],
    ],
    $installer->getTable('sales/order_item') => [
        'columns' => [
            'gift_message_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Gift Message Id',
            ],
            'gift_message_available' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Gift Message Available',
            ],
        ],
    ],
];

$installer->getConnection()->modifyTables($tables);

$installer->endSetup();
