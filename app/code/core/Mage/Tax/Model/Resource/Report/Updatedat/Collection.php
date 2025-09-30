<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Tax
 */

/**
 * Tax report collection
 *
 * @package    Mage_Tax
 */
class Mage_Tax_Model_Resource_Report_Updatedat_Collection extends Mage_Tax_Model_Resource_Report_Collection
{
    /**
     * Aggregated Data Table
     *
     * @var string
     */
    protected $_aggregationTable = 'tax/tax_order_aggregated_updated';
}
