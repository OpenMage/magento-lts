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
 * @copyright  Copyright (c) 2018-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml Grid Renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Longtext extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Render contents as a long text
     *
     * Text will be truncated as specified in string_limit, truncate or 250 by default
     * Also it can be html-escaped and nl2br()
     *
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $truncateLength = 250;
        // stringLength() is for legacy purposes
        if ($this->getColumn()->getStringLimit()) {
            $truncateLength = $this->getColumn()->getStringLimit();
        }
        if ($this->getColumn()->getTruncate()) {
            $truncateLength = $this->getColumn()->getTruncate();
        }
        $text = Mage::helper('core/string')->truncate(parent::_getValue($row), $truncateLength);
        if ($this->getColumn()->getEscape() !== false) {
            $text = $this->escapeHtml($text);
        }
        if ($this->getColumn()->getNl2br()) {
            $text = nl2br($text);
        }
        return $text;
    }
}
