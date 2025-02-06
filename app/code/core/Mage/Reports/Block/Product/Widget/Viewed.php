<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Reports
 */

/**
 * Reports Recently Viewed Products Widget
 *
 * @category   Mage
 * @package    Mage_Reports
 */
class Mage_Reports_Block_Product_Widget_Viewed extends Mage_Reports_Block_Product_Viewed implements Mage_Widget_Block_Interface
{
    protected function _construct()
    {
        parent::_construct();
        $this->addColumnCountLayoutDepend('one_column', 5)
            ->addColumnCountLayoutDepend('two_columns_left', 4)
            ->addColumnCountLayoutDepend('two_columns_right', 4)
            ->addColumnCountLayoutDepend('three_columns', 3);
        $this->addPriceBlockType('bundle', 'bundle/catalog_product_price', 'bundle/catalog/product/price.phtml');
    }
}
