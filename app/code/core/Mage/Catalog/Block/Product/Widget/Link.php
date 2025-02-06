<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Catalog
 */

/**
 * Widget to display link to the product
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
class Mage_Catalog_Block_Product_Widget_Link extends Mage_Catalog_Block_Widget_Link
{
    /**
     * Initialize entity model
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_entityResource = Mage::getResourceSingleton('catalog/product');
    }
}
