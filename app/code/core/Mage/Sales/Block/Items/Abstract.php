<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Abstract block for display sales (quote/order/invoice etc.) items
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Block_Items_Abstract extends Mage_Core_Block_Template
{
    /**
     * Renderers with render type key
     * block    => the block name
     * template => the template file
     * renderer => the block object
     *
     * @var array
     */
    protected $_itemRenders = [];

    /**
     * Initialize default item renderer
     */
    protected function _construct()
    {
        parent::_construct();
        $this->addItemRender('default', 'checkout/cart_item_renderer', 'checkout/cart/item/default.phtml');
    }

    /**
     * Add renderer for item product type
     *
     * @param   string $type
     * @param   string $block
     * @param   string $template
     * @return  $this
     */
    public function addItemRender($type, $block, $template)
    {
        $this->_itemRenders[$type] = [
            'block'     => $block,
            'template'  => $template,
            'renderer'  => null,
        ];

        return $this;
    }

    /**
     * Retrieve item renderer block
     *
     * @param string $type
     * @return Mage_Core_Block_Abstract
     */
    public function getItemRenderer($type)
    {
        if (!isset($this->_itemRenders[$type])) {
            $type = 'default';
        }

        if (is_null($this->_itemRenders[$type]['renderer'])) {
            $this->_itemRenders[$type]['renderer'] = $this->getLayout()
                ->createBlock($this->_itemRenders[$type]['block'])
                ->setTemplate($this->_itemRenders[$type]['template'])
                ->setRenderedBlock($this);
        }
        return $this->_itemRenders[$type]['renderer'];
    }

    /**
     * Prepare item before output
     *
     * @return $this
     */
    protected function _prepareItem(Mage_Core_Block_Abstract $renderer)
    {
        return $this;
    }

    /**
     * Return product type for quote/order item
     *
     * @return string
     */
    protected function _getItemType(Varien_Object $item)
    {
        if ($item->getOrderItem()) {
            $type = $item->getOrderItem()->getProductType();
        } elseif ($item instanceof Mage_Sales_Model_Quote_Address_Item) {
            $type = $item->getQuoteItem()->getProductType();
        } else {
            $type = $item->getProductType();
        }
        return $type;
    }

    /**
     * Get item row html
     *
     * @return  string
     */
    public function getItemHtml(Varien_Object $item)
    {
        $type = $this->_getItemType($item);

        $block = $this->getItemRenderer($type)
            ->setItem($item);
        $this->_prepareItem($block);
        return $block->toHtml();
    }
}
