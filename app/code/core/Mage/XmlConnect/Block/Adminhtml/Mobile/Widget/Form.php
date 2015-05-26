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
 * @package     Mage_XmlConnect
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Xmlconnect widget form block
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Mobile_Widget_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Add color chooser to fieldset
     *
     * @param Varien_Data_Form_Element_Fieldset $fieldset
     * @param string $fieldName
     * @param string $title
     */
    protected function addColor($fieldset, $fieldName, $title)
    {
        $fieldset->addField($fieldName, 'color', array(
            'name'      => $fieldName,
            'label'     => $title,
        ));
    }

    /**
     * Add image uploader to fieldset
     *
     * @param Varien_Data_Form_Element_Fieldset $fieldset
     * @param string $fieldName
     * @param string $title
     * @param string|null $note
     * @param string $default
     * @param boolean $required
     */
    public function addImage($fieldset, $fieldName, $title, $note = null, $default = '', $required = false)
    {
        $fieldset->addField($fieldName, 'image', array(
            'name'      => $fieldName,
            'label'     => $title,
            'note'      => $note,
            'default_value' => $default,
            'required'  => $required,
        ));
    }

    /**
     * Add font selector to fieldset
     *
     * @param Varien_Data_Form_Element_Fieldset $fieldset
     * @param string $fieldPrefix
     * @param string $title
     */
    public function addFont($fieldset, $fieldPrefix, $title)
    {
        $element = $fieldset->addField($fieldPrefix, 'font', array(
            'name'      => $fieldPrefix,
            'label'     => $title,
        ));

        $element->initFields(array(
            'name'      => $fieldPrefix,
            'fontNames' => Mage::helper('xmlconnect')->getDeviceHelper()->getFontList(),
            'fontSizes' => Mage::helper('xmlconnect')->getDeviceHelper()->getFontSizes(),
        ));
    }

    /**
     * Configure image element type
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        $config = Mage::getConfig();
        return array(
            'image' => $config->getBlockClassName('xmlconnect/adminhtml_mobile_form_element_image'),
            'font'  => $config->getBlockClassName('xmlconnect/adminhtml_mobile_form_element_font'),
            'color' => $config->getBlockClassName('xmlconnect/adminhtml_mobile_form_element_color'),
            'tabs'  => $config->getBlockClassName('xmlconnect/adminhtml_mobile_form_element_tabs'),
            'theme' => $config->getBlockClassName('xmlconnect/adminhtml_mobile_form_element_theme'),
            'page'  => $config->getBlockClassName('xmlconnect/adminhtml_mobile_form_element_page'),
            'addrow'=> $config->getBlockClassName('xmlconnect/adminhtml_mobile_form_element_addrow'),
            'datetime' => $config->getBlockClassName('xmlconnect/adminhtml_mobile_form_element_datetime'),
        );
    }
}
