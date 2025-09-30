<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Reports
 */

/**
 * Reports Viewed Product Index Resource Collection
 *
 * @package    Mage_Reports
 */
class Mage_Reports_Model_Resource_Product_Index_Viewed_Collection extends Mage_Reports_Model_Resource_Product_Index_Collection_Abstract
{
    /**
     * Retrieve Product Index table name
     *
     * @return string
     */
    protected function _getTableName()
    {
        return $this->getTable('reports/viewed_product_index');
    }
}
