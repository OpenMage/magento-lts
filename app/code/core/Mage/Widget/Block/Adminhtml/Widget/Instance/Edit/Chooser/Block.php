<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Widget
 */

/**
 * Widget Instance block reference chooser
 *
 * @package    Mage_Widget
 *
 * @method $this setArea(string $value)
 * @method $this setPackage(string $value)
 * @method string getSelected()
 * @method $this setSelected(string $value)
 * @method $this setTheme(string $value)
 */
class Mage_Widget_Block_Adminhtml_Widget_Instance_Edit_Chooser_Block extends Mage_Adminhtml_Block_Widget
{
    protected $_layoutHandlesXml = null;

    /**
     * @var Varien_Simplexml_Element[]
     */
    protected $_layoutHandleUpdates = [];

    /**
     * @var SimpleXMLElement
     */
    protected $_layoutHandleUpdatesXml = null;

    protected $_layoutHandle = [];

    protected $_blocks = [];

    protected $_allowedBlocks = [];

    /**
     * Setter
     *
     * @param array $allowedBlocks
     * @return $this
     */
    public function setAllowedBlocks($allowedBlocks)
    {
        $this->_allowedBlocks = $allowedBlocks;
        return $this;
    }

    /**
     * Add allowed block
     *
     * @param string $block
     * @return $this
     */
    public function addAllowedBlock($block)
    {
        $this->_allowedBlocks[] = $block;
        return $this;
    }

    /**
     * Getter
     *
     * @return array
     */
    public function getAllowedBlocks()
    {
        return $this->_allowedBlocks;
    }

    /**
     * Setter
     * If string given exlopde to array by ',' delimiter
     *
     * @param string|array $layoutHandle
     * @return $this
     */
    public function setLayoutHandle($layoutHandle)
    {
        if (is_string($layoutHandle)) {
            $layoutHandle = explode(',', $layoutHandle);
        }

        $this->_layoutHandle = array_merge(['default'], (array) $layoutHandle);
        return $this;
    }

    /**
     * Getter
     *
     * @return array
     */
    public function getLayoutHandle()
    {
        return $this->_layoutHandle;
    }

    /**
     * Getter
     *
     * @return string
     */
    public function getArea()
    {
        if (!$this->_getData('area')) {
            return Mage_Core_Model_Design_Package::DEFAULT_AREA;
        }

        return $this->_getData('area');
    }

    /**
     * Getter
     *
     * @return string
     */
    public function getPackage()
    {
        if (!$this->_getData('package')) {
            return Mage_Core_Model_Design_Package::DEFAULT_PACKAGE;
        }

        return $this->_getData('package');
    }

    /**
     * Getter
     *
     * @return string
     */
    public function getTheme()
    {
        if (!$this->_getData('theme')) {
            return Mage_Core_Model_Design_Package::DEFAULT_THEME;
        }

        return $this->_getData('theme');
    }

    /**
     * Prepare html output
     *
     * @return string
     */
    protected function _toHtml()
    {
        $selectBlock = $this->getLayout()->createBlock('core/html_select')
            ->setName('block')
            ->setClass('required-entry select')
            ->setExtraParams('onchange="WidgetInstance.loadSelectBoxByType(\'block_template\','
                . ' this.up(\'div.group_container\'), this.value)"')
            ->setOptions($this->getBlocks())
            ->setValue($this->getSelected());
        return parent::_toHtml() . $selectBlock->toHtml();
    }

    /**
     * Retrieve blocks array
     *
     * @return array
     */
    public function getBlocks()
    {
        if (empty($this->_blocks)) {
            $update = Mage::getModel('core/layout')->getUpdate();
            $this->_layoutHandlesXml = $update->getFileLayoutUpdatesXml(
                $this->getArea(),
                $this->getPackage(),
                $this->getTheme(),
            );
            $this->_collectLayoutHandles();
            $this->_collectBlocks();
            array_unshift($this->_blocks, [
                'value' => '',
                'label' => Mage::helper('widget')->__('-- Please Select --'),
            ]);
        }

        return $this->_blocks;
    }

    /**
     * Merging layout handles and create xml of merged layout handles
     */
    protected function _collectLayoutHandles()
    {
        foreach ($this->getLayoutHandle() as $handle) {
            $this->_mergeLayoutHandles($handle);
        }

        $updatesStr = '<?xml version="1.0"?><layout>' . implode('', $this->_layoutHandleUpdates) . '</layout>';
        $this->_layoutHandleUpdatesXml = simplexml_load_string($updatesStr, 'Varien_Simplexml_Element');
    }

    /**
     * Adding layout handle that specified in node 'update' to general layout handles
     *
     * @param string $handle
     */
    public function _mergeLayoutHandles($handle)
    {
        foreach ($this->_layoutHandlesXml->{$handle} as $updateXml) {
            foreach ($updateXml->children() as $child) {
                if (strtolower($child->getName()) == 'update' && isset($child['handle'])) {
                    $this->_mergeLayoutHandles((string) $child['handle']);
                }
            }

            $this->_layoutHandleUpdates[] = $updateXml->asNiceXml();
        }
    }

    /**
     * Filter and collect blocks into array
     */
    protected function _collectBlocks()
    {
        if ($blocks = $this->_layoutHandleUpdatesXml->xpath('//block/label/..')) {
            /** @var Mage_Core_Model_Layout_Element $block */
            foreach ($blocks as $block) {
                if ((string) $block->getAttribute('name') && $this->_filterBlock($block)) {
                    $helper = Mage::helper(Mage_Core_Model_Layout::findTranslationModuleName($block));
                    $this->_blocks[(string) $block->getAttribute('name')] = $helper->__((string) $block->label);
                }
            }
        }

        asort($this->_blocks, SORT_STRING);
    }

    /**
     * Check whether given block match allowed block types
     *
     * @param Mage_Core_Model_Layout_Element $block
     * @return bool
     */
    protected function _filterBlock($block)
    {
        if (!$this->getAllowedBlocks()) {
            return true;
        }

        if (in_array((string) $block->getAttribute('name'), $this->getAllowedBlocks())) {
            return true;
        }

        return false;
    }
}
