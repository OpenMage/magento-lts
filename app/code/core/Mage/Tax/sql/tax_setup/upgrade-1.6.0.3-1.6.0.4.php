<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Tax
 */

/** @var Mage_Tax_Model_Resource_Setup $this */

/**
 * Add new field to 'tax/tax_calculation_rule'
 */
$this->getConnection()
    ->addColumn(
        $this->getTable('tax/tax_calculation_rule'),
        'calculate_subtotal',
        [
            'TYPE' => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'NULLABLE' => false,
            'COMMENT' => 'Calculate off subtotal option',
        ],
    );
