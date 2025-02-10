<?php
/**
 * Catalog layer category filter
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Catalog
 */
class Mage_Catalog_Block_Layer_Filter_Category extends Mage_Catalog_Block_Layer_Filter_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->_filterModelName = 'catalog/layer_filter_category';
    }
}
