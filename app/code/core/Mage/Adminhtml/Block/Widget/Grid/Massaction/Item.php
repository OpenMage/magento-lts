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
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Grid widget massaction single action item
 *
 * @category   Mage
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
