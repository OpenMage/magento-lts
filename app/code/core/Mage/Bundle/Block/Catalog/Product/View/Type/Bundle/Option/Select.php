<?php
/**
 * Bundle option dropdown type renderer
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Bundle
 */
class Mage_Bundle_Block_Catalog_Product_View_Type_Bundle_Option_Select extends Mage_Bundle_Block_Catalog_Product_View_Type_Bundle_Option
{
    /**
     * Set template
     */
    protected function _construct()
    {
        $this->setTemplate('bundle/catalog/product/view/type/bundle/option/select.phtml');
    }
}
