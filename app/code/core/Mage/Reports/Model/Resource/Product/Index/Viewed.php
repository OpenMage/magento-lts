<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Reports
 */

/**
 * Reports Viewed Product Index Resource Model
 *
 * @category   Mage
 * @package    Mage_Reports
 */
class Mage_Reports_Model_Resource_Product_Index_Viewed extends Mage_Reports_Model_Resource_Product_Index_Abstract
{
    protected function _construct()
    {
        $this->_init('reports/viewed_product_index', 'index_id');
    }
}
