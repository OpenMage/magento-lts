<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Attribute form apply element
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
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
              . '<option value="0">' . $this->getModeLabels('all'). '</option>'
              . '<option value="1" ' . ($this->getValue()==null ? '' : 'selected') . '>' . $this->getModeLabels('custom'). '</option>'
              . '</select><br /><br />';

        $html .= parent::getElementHtml();
        return $html;
    }

    /**
     * Dublicate interface of Varien_Data_Form_Element_Abstract::setReadonly
     *
     * @param bool $readonly
     * @param bool $useDisabled
     * @return Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Apply
     */
    public function setReadonly($readonly, $useDisabled = false)
    {
        $this->setData('readonly', $readonly);
        $this->setData('disabled', $useDisabled);
        return $this;
    }

}
