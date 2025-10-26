<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Label & link block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Urlrewrite_Link extends Mage_Core_Block_Abstract
{
    /**
     * Render output
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->getItem()) {
            return '<p>' . $this->getLabel() . ' <a href="' . $this->getItemUrl() . '">'
                . $this->escapeHtml($this->getItem()->getName()) . '</a></p>';
        }

        return '';
    }
}
