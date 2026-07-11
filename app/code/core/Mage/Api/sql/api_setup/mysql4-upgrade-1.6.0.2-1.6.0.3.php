<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api
 */

/** @var Mage_Core_Model_Resource_Setup $this */
$this->startSetup();

$this->getConnection()->changeColumn(
    $this->getTable('api/user'),
    'lognum',
    'lognum',
    [
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'unsigned' => true,
        'nullable' => false,
        'default' => '0',
        'comment' => 'Quantity of log ins',
    ],
);

$this->endSetup();
