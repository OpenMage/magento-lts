<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api
 */

/** @var Mage_Core_Model_Resource_Setup $this */
$this->startSetup();

$table = $this->getTable('api/session');

$this->getConnection()->changeColumn(
    $table,
    'sessid',
    'sessid',
    [
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length'    => 64,
        'nullable'  => false,
        'comment'   => 'Session ID',
    ],
);

$this->endSetup();
