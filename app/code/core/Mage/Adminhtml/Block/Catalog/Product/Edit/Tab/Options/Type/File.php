<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * customers defined options
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Options_Type_File extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Options_Type_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('catalog/product/edit/options/type/file.phtml');
    }
}
