<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_GiftMessage
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
