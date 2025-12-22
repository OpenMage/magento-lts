<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Widget
 */

/**
 * Widget Instance layouts chooser
 *
 * @package    Mage_Widget
 *
 * @method string getSelectName()
 * @method $this  setArea(string $value)
 * @method $this  setPackage(string $value)
 * @method $this  setSelectName(string $value)
 * @method $this  setTheme(string $value)
 */
class Mage_Widget_Block_Adminhtml_Widget_Instance_Edit_Chooser_Layout extends Mage_Adminhtml_Block_Widget
{
    protected $_layoutHandles = [];

    /**
     * layout handles wildcar patterns
     *
     * @var array
     */
    protected $_layoutHandlePatterns = [
        '^default$',
        '^catalog_category_*',
        '^catalog_product_*',
        '^PRODUCT_*',
    ];

    /**
     * Add not allowed layout handle pattern
     *
     * @param  string $pattern
     * @return $this
     */
    public function addLayoutHandlePattern($pattern)
    {
        $this->_layoutHandlePatterns[] = $pattern;
        return $this;
    }

    /**
     * Getter
     *
     * @return array
     */
    public function getLayoutHandlePatterns()
    {
        return $this->_layoutHandlePatterns;
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
            ->setName($this->getSelectName())
            ->setId('layout_handle')
            ->setClass('required-entry select')
            ->setExtraParams("onchange=\"WidgetInstance.loadSelectBoxByType(\'block_reference\', "
                            . "this.up(\'div.pages\'), this.value)\"")
            ->setOptions($this->getLayoutHandles(
                $this->getArea(),
                $this->getPackage(),
                $this->getTheme(),
            ));
        return parent::_toHtml() . $selectBlock->toHtml();
    }

    /**
     * Retrieve layout handles
     *
     * @param  string $area
     * @param  string $package
     * @param  string $theme
     * @return array
     */
    public function getLayoutHandles($area, $package, $theme)
    {
        if (empty($this->_layoutHandles)) {
            /** @var Mage_Core_Model_Layout_Update $update */
            $update = Mage::getModel('core/layout')->getUpdate();
            $this->_layoutHandles[''] = Mage::helper('widget')->__('-- Please Select --');
            $this->_collectLayoutHandles($update->getFileLayoutUpdatesXml($area, $package, $theme));
        }

        return $this->_layoutHandles;
    }

    /**
     * Filter and collect layout handles into array
     *
     * @param SimpleXMLElement $layoutHandles
     */
    protected function _collectLayoutHandles($layoutHandles)
    {
        if ($layoutHandlesArr = $layoutHandles->xpath('/*/*/label/..')) {
            /** @var Varien_Simplexml_Element $node */
            foreach ($layoutHandlesArr as $node) {
                if ($this->_filterLayoutHandle($node->getName())) {
                    $helper = Mage::helper(Mage_Core_Model_Layout::findTranslationModuleName($node));
                    $this->_layoutHandles[$node->getName()] = $this->helper('core')->jsQuoteEscape(
                        $helper->__((string) $node->label),
                    );
                }
            }

            asort($this->_layoutHandles, SORT_STRING);
        }
    }

    /**
     * Check if given layout handle allowed (do not match not allowed patterns)
     *
     * @param  string $layoutHandle
     * @return bool
     */
    protected function _filterLayoutHandle($layoutHandle)
    {
        $wildCard = '/(' . implode(')|(', $this->getLayoutHandlePatterns()) . ')/';
        if (preg_match($wildCard, $layoutHandle)) {
            return false;
        }

        return true;
    }
}
