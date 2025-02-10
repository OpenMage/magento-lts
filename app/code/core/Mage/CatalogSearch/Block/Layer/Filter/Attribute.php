<?php
/**
 * CatalogSearch attribute layer filter
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_CatalogSearch
 */
class Mage_CatalogSearch_Block_Layer_Filter_Attribute extends Mage_Catalog_Block_Layer_Filter_Attribute
{
    /**
     * Set filter model name
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->_filterModelName = 'catalogsearch/layer_filter_attribute';
    }
}
