<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Grid widget massaction single action item
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Widget_Grid_Massaction_Item extends Mage_Adminhtml_Block_Widget
{
    protected $_massaction = null;

    /**
     * Set parent massaction block
     *
     * @param  Mage_Adminhtml_Block_Widget_Grid_Massaction_Abstract $massaction
     * @return $this
     */
    public function setMassaction($massaction)
    {
        $this->_massaction = $massaction;
        return $this;
    }

    /**
     * Retrieve parent massaction block
     *
     * @return Mage_Adminhtml_Block_Widget_Grid_Massaction_Abstract
     */
    public function getMassaction()
    {
        return $this->_massaction;
    }

    /**
     * Set additional action block for this item
     *
     * @param string|Mage_Core_Block_Abstract $block
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function setAdditionalActionBlock($block)
    {
        if (is_string($block)) {
            $block = $this->getLayout()->createBlock($block);
        } elseif (is_array($block)) {
            $block = $this->_createFromConfig($block);
        } elseif (!($block instanceof Mage_Core_Block_Abstract)) {
            Mage::throwException('Unknown block type');
        }

        $this->setChild('additional_action', $block);
        return $this;
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid_Massaction_Item_Additional_Default
     */
    protected function _createFromConfig(array $config)
    {
        /** @var Mage_Adminhtml_Block_Widget_Grid_Massaction_Item_Additional_Default $block */
        $block = $this->getLayout()->createBlock('adminhtml/widget_grid_massaction_item_additional_default');
        $block->createFromConfiguration(isset($config['type']) ? $config['config'] : $config);
        return $block;
    }

    /**
     * Retrieve additional action block for this item
     *
     * @return Mage_Core_Block_Abstract
     */
    public function getAdditionalActionBlock()
    {
        return $this->getChild('additional_action');
    }

    /**
     * Retrieve additional action block HTML for this item
     *
     * @return string
     */
    public function getAdditionalActionBlockHtml()
    {
        return $this->getChildHtml('additional_action');
    }
}
