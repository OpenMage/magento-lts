<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Widget to display catalog link
 *
 * @package    Mage_Catalog
 *
 * @method int getStoreId()
 * @method bool hasStoreId()
 */
class Mage_Catalog_Block_Widget_Link extends Mage_Core_Block_Html_Link implements Mage_Widget_Block_Interface
{
    /**
     * Entity model name which must be used to retrieve entity specific data.
     * @var null|Mage_Catalog_Model_Resource_Eav_Mysql4_Abstract
     */
    protected $_entityResource = null;

    /**
     * Prepared href attribute
     *
     * @var string
     */
    protected $_href;

    /**
     * Prepared anchor text
     *
     * @var string
     */
    protected $_anchorText;

    /**
     * Prepare url using passed id and return it
     * or return false if path was not found.
     *
     * @return false|string
     * @throws Mage_Core_Exception
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getHref()
    {
        if (!$this->_href) {
            if ($this->hasStoreId()) {
                $store = Mage::app()->getStore($this->getStoreId());
            } else {
                $store = Mage::app()->getStore();
            }

            $idPath = explode('/', $this->_getData('id_path'));

            if (isset($idPath[0]) && isset($idPath[1]) && $idPath[0] == 'product') {
                /** @var Mage_Catalog_Helper_Product $helper */
                $helper = $this->_getFactory()->getHelper('catalog/product');
                $productId = $idPath[1];
                $categoryId = $idPath[2] ?? null;

                $this->_href = $helper->getFullProductUrl($productId, $categoryId);
            } elseif (isset($idPath[0]) && isset($idPath[1]) && $idPath[0] == 'category') {
                $categoryId = $idPath[1];
                if ($categoryId) {
                    /** @var Mage_Catalog_Helper_Category $helper */
                    $helper = $this->_getFactory()->getHelper('catalog/category');
                    $category = Mage::getModel('catalog/category')->load($categoryId);
                    $this->_href = $helper->getCategoryUrl($category);
                }
            }
        }

        if ($this->_href) {
            if (!str_contains($this->_href, '___store')) {
                $symbol = (!str_contains($this->_href, '?')) ? '?' : '&';
                $this->_href = $this->_href . $symbol . '___store=' . $store->getCode();
            }
        } else {
            return false;
        }

        return $this->_href;
    }

    /**
     * Prepare anchor text using passed text as parameter.
     * If anchor text was not specified get entity name from DB.
     *
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getAnchorText()
    {
        if ($this->hasStoreId()) {
            $store = Mage::app()->getStore($this->getStoreId());
        } else {
            $store = Mage::app()->getStore();
        }

        if (!$this->_anchorText && $this->_entityResource) {
            if (!$this->_getData('anchor_text')) {
                $idPath = explode('/', $this->_getData('id_path'));
                if (isset($idPath[1])) {
                    $entityId = $idPath[1];
                    if ($entityId) {
                        $this->_anchorText = $this->_entityResource
                            ->getAttributeRawValue((int) $entityId, 'name', $store);
                    }
                }
            } else {
                $this->_anchorText = $this->_getData('anchor_text');
            }
        }

        return $this->_anchorText;
    }

    /**
     * Render block HTML
     * or return empty string if url can't be prepared
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->getHref()) {
            return parent::_toHtml();
        }

        return '';
    }
}
