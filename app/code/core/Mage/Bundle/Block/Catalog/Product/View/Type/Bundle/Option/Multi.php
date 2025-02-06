<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Bundle
 */

/**
 * Bundle option multi select type renderer
 *
 * @category   Mage
 * @package    Mage_Bundle
 */
class Mage_Bundle_Block_Catalog_Product_View_Type_Bundle_Option_Multi extends Mage_Bundle_Block_Catalog_Product_View_Type_Bundle_Option
{
    /**
     * Set template
     */
    protected function _construct()
    {
        $this->setTemplate('bundle/catalog/product/view/type/bundle/option/multi.phtml');
    }
}
