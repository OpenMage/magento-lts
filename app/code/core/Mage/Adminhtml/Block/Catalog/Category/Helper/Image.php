<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Category form image field helper
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Category_Helper_Image extends Varien_Data_Form_Element_Image
{
    /**
     * @inheritDoc
     */
    protected function _getUrl()
    {
        if ($this->getValue()) {
            return Mage::getBaseUrl('media') . 'catalog/category/' . $this->getValue();
        }

        return null;
    }
}
