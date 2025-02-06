<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Catalog
 */

/**
 * Catalog Layer Decimal Attribute Filter Block
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
class Mage_Catalog_Block_Layer_Filter_Decimal extends Mage_Catalog_Block_Layer_Filter_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->_filterModelName = 'catalog/layer_filter_decimal';
    }

    /**
     * Prepare filter process
     *
     * @return $this
     */
    protected function _prepareFilter()
    {
        $this->_filter->setAttributeModel($this->getAttributeModel());
        return $this;
    }
}
