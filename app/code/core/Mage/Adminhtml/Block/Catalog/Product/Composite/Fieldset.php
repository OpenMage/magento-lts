<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml block for showing product options fieldsets
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_Composite_Fieldset extends Mage_Core_Block_Text_List
{
    /**
     *
     * Iterates through fieldsets and fetches complete html
     *
     * @return string
     */
    protected function _toHtml()
    {
        $children = $this->getSortedChildren();
        $total = count($children);
        $i = 0;
        $this->setText('');
        foreach ($children as $name) {
            $block = $this->getLayout()->getBlock($name);
            if (!$block) {
                Mage::throwException(Mage::helper('core')->__('Invalid block: %s', $name));
            }

            $i++;
            $block->setIsLastFieldset($i == $total);

            $this->addText($block->toHtml());
        }

        return parent::_toHtml();
    }
}
