<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Tag
 */

/**
 * @package    Mage_Tag
 */
class Mage_Tag_Block_Product_List extends Mage_Core_Block_Template
{
    protected $_collection;

    /**
     * Unique Html Id
     *
     * @var null|string
     */
    protected $_uniqueHtmlId = null;

    /**
     * @throws Mage_Core_Model_Store_Exception
     * @return int
     */
    public function getCount()
    {
        return count($this->getTags());
    }

    /**
     * @throws Mage_Core_Model_Store_Exception
     * @return mixed
     */
    public function getTags()
    {
        return $this->_getCollection()->getItems();
    }

    /**
     * @return bool
     */
    public function getProductId()
    {
        if ($product = Mage::registry('current_product')) {
            return $product->getId();
        }

        return false;
    }

    /**
     * @throws Mage_Core_Model_Store_Exception
     * @return mixed
     */
    protected function _getCollection()
    {
        if (!$this->_collection && $this->getProductId()) {
            $model = Mage::getModel('tag/tag');
            $this->_collection = $model->getResourceCollection()
                ->addPopularity()
                ->addStatusFilter($model->getApprovedStatus())
                ->addProductFilter($this->getProductId())
                ->setFlag('relation', true)
                ->addStoreFilter(Mage::app()->getStore()->getId())
                ->setActiveFilter()
                ->load();
        }

        return $this->_collection;
    }

    /**
     * @inheritDoc
     */
    protected function _beforeToHtml()
    {
        if (!$this->getProductId()) {
            return $this;
        }

        return parent::_beforeToHtml();
    }

    /**
     * @return string
     */
    public function getFormAction()
    {
        return Mage::getUrl('tag/index/save', [
            'product' => $this->getProductId(),
            Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => Mage::helper('core/url')->getEncodedUrl(),
            '_secure' => $this->_isSecure(),
        ]);
    }

    /**
     * Render tags by specified pattern and implode them by specified 'glue' string
     *
     * @param string $pattern
     * @param string $glue
     * @throws Mage_Core_Model_Store_Exception
     * @return string
     */
    public function renderTags($pattern, $glue = ' ')
    {
        $out = [];
        foreach ($this->getTags() as $tag) {
            $out[] = sprintf(
                $pattern,
                $tag->getTaggedProductsUrl(),
                $this->escapeHtml($tag->getName()),
                $tag->getProducts(),
            );
        }

        return implode($glue, $out);
    }

    /**
     * Generate unique html id
     *
     * @param string $prefix
     * @return string
     */
    public function getUniqueHtmlId($prefix = '')
    {
        if (is_null($this->_uniqueHtmlId)) {
            $this->_uniqueHtmlId = Mage::helper('core/data')->uniqHash($prefix);
        }

        return $this->_uniqueHtmlId;
    }
}
