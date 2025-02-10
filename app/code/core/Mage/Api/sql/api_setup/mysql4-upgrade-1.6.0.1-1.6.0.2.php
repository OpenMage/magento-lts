<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Core_Model_Resource_Setup $this
 */
$this->startSetup();

$this->getConnection()->changeColumn(
    $this->getTable('api/user'),
    'api_key',
    'api_key',
    [
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255,
        'comment' => 'Api key',
    ],
);

$this->endSetup();
