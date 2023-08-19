<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml grid item renderer product image
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    protected $_defaultWidth = 128;

    public function render(Varien_Object $row)
    {
        $image = $this->_checkImageIsSelected($row);
        if (!$image) {
            return '';
            //return '<img src="' . Mage::getStoreConfig('catalog/placeholder/image_placeholder') . ' " />';
        }
        $imageDimensions = $this->getColumn()->getWidth() ?: $this->_defaultWidth;
        $imageSrc = $this->_getHelperImage($image)
        ->resize($imageDimensions, $imageDimensions);
        $imageUrl = $this->_getImageUrl($image);
        $result = '';
        $result .= '<a href="' . $imageUrl . '" title="' . basename($imageUrl) . '" target="_blank">';
            $result .= '<img src="' . $imageSrc . '" alt="' . basename($imageUrl) . '" title="' . basename($imageUrl) . '" width="' . $imageDimensions . '" height="' . $imageDimensions . '"/>';
        $result .= '</a>';
        return $result;
    }

    public function renderExport(Varien_Object $row) : string
    {
        $image = $this->_checkImageIsSelected($row);
        if (!$image) {
            return '';
        }
        return $this->_getImageUrl($image);
    }

    public function renderCss()
    {
        return parent::renderCss() . ' a-center';
    }

    protected function _getHelperImage($image) : Mage_Catalog_Helper_Image
    {
        $dummyProduct = Mage::getModel('catalog/product');
        return Mage::helper('catalog/image')->init($dummyProduct, $this->getColumn()->getAttributeCode(), $image);
    }

    protected function _getImageUrl($image) : string {
        if (!$image) {
            return '';
        }
        return Mage::getBaseUrl('media') . 'catalog/product/' . $image;
    }

    private function _checkImageIsSelected($row) : string|null
    {
        $value = $this->_getValue($row);
        if (!$value || $value == 'no_selection') return false;
        return $value;
    }
}
