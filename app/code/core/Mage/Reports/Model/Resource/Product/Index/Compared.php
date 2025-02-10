<?php
/**
 * Reports Compared Product Index Resource Model
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Reports
 */
class Mage_Reports_Model_Resource_Product_Index_Compared extends Mage_Reports_Model_Resource_Product_Index_Abstract
{
    protected function _construct()
    {
        $this->_init('reports/compared_product_index', 'index_id');
    }
}
