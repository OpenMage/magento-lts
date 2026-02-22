<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Source_Catalog_ImageDuplicate
{
    public function toOptionArray()
    {
        return [
            ['value' => Mage_Catalog_Model_Product_Image::ON_DUPLICATE_ASK, 'label' => Mage::helper('adminhtml')->__('Always ask')],
            ['value' => Mage_Catalog_Model_Product_Image::ON_DUPLICATE_SKIP, 'label' => Mage::helper('adminhtml')->__('Duplicate product without images')],
            ['value' => Mage_Catalog_Model_Product_Image::ON_DUPLICATE_COPY, 'label' => Mage::helper('adminhtml')->__('Duplicate product with images')],
        ];
    }
}
