<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Catalog textarea attribute WYSIWYG button
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Helper_Form_Wysiwyg extends Varien_Data_Form_Element_Textarea
{
    /**
     * Retrieve additional html and put it at the end of element html
     *
     * @return string
     */
    public function getAfterElementHtml()
    {
        $html = parent::getAfterElementHtml();
        if ($this->getIsWysiwygEnabled()) {
            $disabled = ($this->getDisabled() || $this->getReadonly());
            $html .= Mage::getSingleton('core/layout')
                ->createBlock('adminhtml/widget_button', '', [
                    'label'   => Mage::helper('catalog')->__('WYSIWYG Editor'),
                    'type'    => 'button',
                    'disabled' => $disabled,
                    'class' => 'btn-wysiwyg',
                    'onclick' => "catalogWysiwygEditor.open('" . Mage::helper('adminhtml')->getUrl('*/*/wysiwyg') . "', '" . $this->getHtmlId() . "')",
                ])->toHtml();
        }

        return $html;
    }

    /**
     * Check whether wysiwyg enabled or not
     *
     * @return bool
     */
    public function getIsWysiwygEnabled()
    {
        if (Mage::helper('catalog')->isModuleEnabled('Mage_Cms')) {
            return (bool) (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()
                && $this->getEntityAttribute()->getIsWysiwygEnabled());
        }

        return false;
    }
}
