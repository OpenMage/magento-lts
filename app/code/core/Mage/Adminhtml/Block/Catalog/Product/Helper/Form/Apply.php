<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Attribute form apply element
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Apply extends Varien_Data_Form_Element_Multiselect
{
    public function getElementHtml()
    {
        $elementAttributeHtml = '';

        if ($this->getReadonly()) {
            $elementAttributeHtml = $elementAttributeHtml . ' readonly="readonly"';
        }

        if ($this->getDisabled()) {
            $elementAttributeHtml = $elementAttributeHtml . ' disabled="disabled"';
        }

        $html = '<select onchange="toggleApplyVisibility(this)"' . $elementAttributeHtml . '>'
              . '<option value="0">' . $this->getModeLabels('all') . '</option>'
              . '<option value="1" ' . ($this->getValue() == null ? '' : 'selected') . '>' . $this->getModeLabels('custom') . '</option>'
              . '</select><br /><br />';
        return $html . parent::getElementHtml();
    }

    /**
     * Duplicate interface of Varien_Data_Form_Element_Abstract::setReadonly
     *
     * @param bool $readonly
     * @param bool $useDisabled
     * @return $this
     */
    public function setReadonly($readonly, $useDisabled = false)
    {
        $this->setData('readonly', $readonly);
        $this->setData('disabled', $useDisabled);
        return $this;
    }
}
