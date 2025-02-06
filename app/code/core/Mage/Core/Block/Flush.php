<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Core
 */

/**
 * Immediate flush block. To be used only as root
 *
 * @category   Mage
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
