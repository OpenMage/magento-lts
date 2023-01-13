<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml grid item renderer line to wrap
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
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
     * @param Varien_Object $row
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
            $wrappedLine .= Mage::helper('core/string')->substr($line, ($lineLength * $i), $lineLength) . "<br />";
        }
        return $wrappedLine;
    }
}
