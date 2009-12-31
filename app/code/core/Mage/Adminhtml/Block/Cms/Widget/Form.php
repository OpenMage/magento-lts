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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_GoogleBase
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * CMS WYSIWYG widget plugin form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Cms_Widget_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Form with widget to select
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend'    => $this->helper('cms')->__('Widget')
        ));

        $this->setEmptyOptionDescription($this->helper('cms')->__('Please select a Widget Type'));
        $select = $fieldset->addField('select_widget_type', 'select', array(
            'label'                 => $this->helper('cms')->__('Widget Type'),
            'title'                 => $this->helper('cms')->__('Widget Type'),
            'name'                  => 'widget_type',
            'required'              => true,
            'options'               => $this->_getWidgetSelectOptions(),
            'note'                  => $this->getEmptyOptionDescription(),
            'after_element_html'    => $this->_getWidgetSelectAfterHtml(),
        ));

        $form->setUseContainer(true);
        $form->setId('edit_form');
        $form->setMethod('post');
        $form->setAction($this->getUrl('*/*/buildWidget'));
        $this->setForm($form);
    }

    /**
     * Prepare options for widgets HTML select
     *
     * @return array
     */
    protected function _getWidgetSelectOptions()
    {
        foreach ($this->_getAvailableWidgets(true) as $data) {
            $options[$data['type']] = $data['name'];
        }
        return $options;
    }

    /**
     * Prepare widgets select after element HTML
     *
     * @return string
     */
    protected function _getWidgetSelectAfterHtml()
    {
        $html = '';
        $i = 0;
        foreach ($this->_getAvailableWidgets(true) as $data) {
            $html .= sprintf('<div id="widget-description-%s" class="no-display">%s</div>', $i, $data['description']);
            $i++;
        }
        return $html;
    }

    /**
     * Return array of available widgets based on configuration
     *
     * @return array
     */
    protected function _getAvailableWidgets($withEmptyElement = false)
    {
        if (!$this->getData('available_widgets')) {
            $config = Mage::getSingleton('cms/widget')->getXmlConfig();
            $widgets = $config->getNode('widgets');
            $result = array();
            if ($withEmptyElement) {
                $result[] = array(
                    'type'        => '',
                    'name'        => $this->helper('adminhtml')->__('-- Please Select --'),
                    'description' => $this->getEmptyOptionDescription(),
                );
            }
            $skipped = $this->_getSkippedWidgets();
            foreach ($widgets->children() as $widget) {
                if ($widget->is_context && $this->_skipContextWidgets()) {
                    continue;
                }
                if (is_array($skipped) && in_array($widget->getAttribute('type'), $skipped)) {
                    continue;
                }
                if ($widget->getAttribute('module')) {
                    $helper = Mage::helper($widget->getAttribute('module'));
                } else {
                    $helper = Mage::helper('cms');
                }
                $result[$widget->getName()] = array(
                    'name'          => $helper->__((string)$widget->name),
                    'code'          => $widget->getName(),
                    'type'          => $widget->getAttribute('type'),
                    'description'   => $helper->__((string)$widget->description),
                );
            }
            usort($result, array($this, "_sortWidgets"));
            $this->setData('available_widgets', $result);
        }
        return $this->getData('available_widgets');
    }

    /**
     * Disable insertion of context(is_context) widgets or not
     *
     * @return bool
     */
    protected function _skipContextWidgets()
    {
        return (bool)$this->getParentBlock()->getData('skip_context_widgets');
    }

    /**
     * Return array of widgets disabled for selection
     *
     * @return array
     */
    protected function _getSkippedWidgets()
    {
        $skipped = $this->getParentBlock()->getData('skip_widgets');
        if (is_array($skipped)) {
            return $skipped;
        }
        $skipped = Mage::helper('core')->urlDecode($skipped);
        $skipped = explode(',', $skipped);
        return $skipped;
    }

    /**
     * User-defined widgets sorting by Name
     *
     * @param array $a
     * @param array $b
     * @return boolean
     */
    protected function _sortWidgets($a, $b)
    {
        return strcmp($a["name"], $b["name"]);
    }
}
