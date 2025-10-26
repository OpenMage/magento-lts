<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * Immediate flush block. To be used only as root
 *
 * @package    Mage_Core
 */
class Mage_Core_Block_Flush extends Mage_Core_Block_Abstract
{
    /**
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->_beforeToHtml()) {
            return '';
        }

        ob_implicit_flush();

        foreach ($this->getSortedChildren() as $name) {
            $block = $this->getLayout()->getBlock($name);
            if (!$block) {
                Mage::exception(Mage::helper('core')->__('Invalid block: %s', $name));
            }

            echo $block->toHtml();
        }

        return '';
    }
}
