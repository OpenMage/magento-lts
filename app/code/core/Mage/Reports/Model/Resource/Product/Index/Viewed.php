<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Reports
 */

/**
 * Reports Viewed Product Index Resource Model
 *
 * @package    Mage_Reports
 */
class Mage_Reports_Model_Resource_Product_Index_Viewed extends Mage_Reports_Model_Resource_Product_Index_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('reports/viewed_product_index', 'index_id');
    }
}
