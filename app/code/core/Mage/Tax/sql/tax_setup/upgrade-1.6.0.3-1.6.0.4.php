<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
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
