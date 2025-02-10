<?php
/**
 * customers defined options
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Options_Type_Date extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Options_Type_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('catalog/product/edit/options/type/date.phtml');
    }
}
