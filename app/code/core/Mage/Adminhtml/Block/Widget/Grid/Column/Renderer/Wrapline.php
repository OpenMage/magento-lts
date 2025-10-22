<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml grid item renderer line to wrap
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Wrapline extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Default max length of a line at one row
     *
     * @var int
     */
    protected $_defaultMaxLineLength = 60;

    /**
     * Renders grid column
     *
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $line = parent::_getValue($row);
        $wrappedLine = '';
        $lineLength = $this->getColumn()->getData('lineLength')
            ? $this->getColumn()->getData('lineLength')
            : $this->_defaultMaxLineLength;
        for ($i = 0, $n = floor(Mage::helper('core/string')->strlen($line) / $lineLength); $i <= $n; $i++) {
            $wrappedLine .= Mage::helper('core/string')->substr($line, ($lineLength * $i), $lineLength) . '<br />';
        }

        return $wrappedLine;
    }
}
