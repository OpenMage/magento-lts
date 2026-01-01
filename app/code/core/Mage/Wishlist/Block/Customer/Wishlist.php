<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Wishlist
 */

/**
 * Wishlist block customer items
 *
 * @package    Mage_Wishlist
 */
class Mage_Wishlist_Block_Customer_Wishlist extends Mage_Wishlist_Block_Abstract
{
    /**
     * List of product options rendering configurations by product type
     */
    protected $_optionsCfg = [];

    /**
     * Add wishlist conditions to collection
     *
     * @param  Mage_Wishlist_Model_Resource_Item_Collection $collection
     * @return $this
     */
    protected function _prepareCollection($collection)
    {
        $collection->setInStockFilter(true)->setOrder('added_at', 'ASC');
        return $this;
    }

    /**
     * Preparing global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle($this->__('My Wishlist'));
        }

        return $this;
    }

    /**
     * Retrieve Back URL
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('customer/account/');
    }

    /**
     * Sets all options render configurations
     *
     * @param  null|array $optionCfg
     * @return $this
     * @deprecated after 1.6.2.0
     */
    public function setOptionsRenderCfgs($optionCfg)
    {
        $this->_optionsCfg = $optionCfg;
        return $this;
    }

    /**
     * Returns all options render configurations
     *
     * @return array
     * @deprecated after 1.6.2.0
     */
    public function getOptionsRenderCfgs()
    {
        return $this->_optionsCfg;
    }

    /**
     * Adds config for rendering product type options
     *
     * @param  string      $productType
     * @param  string      $helperName
     * @param  null|string $template
     * @return $this
     * @deprecated after 1.6.2.0
     */
    public function addOptionsRenderCfg($productType, $helperName, $template = null)
    {
        $this->_optionsCfg[$productType] = ['helper' => $helperName, 'template' => $template];
        return $this;
    }

    /**
     * Returns html for showing item options
     *
     * @param  string     $productType
     * @return null|array
     * @deprecated after 1.6.2.0
     */
    public function getOptionsRenderCfg($productType)
    {
        return $this->_optionsCfg[$productType] ?? $this->_optionsCfg['default'] ?? null;
    }

    /**
     * Returns html for showing item options
     *
     * @return string
     * @deprecated after 1.6.2.0
     */
    public function getDetailsHtml(Mage_Wishlist_Model_Item $item)
    {
        $cfg = $this->getOptionsRenderCfg($item->getProduct()->getTypeId());
        if (!$cfg) {
            return '';
        }

        $helper = Mage::helper($cfg['helper']);
        if (!($helper instanceof Mage_Catalog_Helper_Product_Configuration_Interface)) {
            Mage::throwException($this->__("Helper for wishlist options rendering doesn't implement required interface."));
        }

        $block = $this->getChild('item_options');
        if (!$block) {
            return '';
        }

        if ($cfg['template']) {
            $template = $cfg['template'];
        } else {
            $cfgDefault = $this->getOptionsRenderCfg('default');
            if (!$cfgDefault) {
                return '';
            }

            $template = $cfgDefault['template'];
        }

        return $block->setTemplate($template)
            ->setOptionList($helper->getOptions($item))
            ->toHtml();
    }

    /**
     * Returns qty to show visually to user
     *
     * @return float
     * @deprecated after 1.6.2.0
     */
    public function getAddToCartQty(Mage_Wishlist_Model_Item $item)
    {
        $qty = $this->getQty($item);
        return $qty ? $qty : 1;
    }
}
