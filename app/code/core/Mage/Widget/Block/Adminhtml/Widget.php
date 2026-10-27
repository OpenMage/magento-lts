<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Widget
 */

/**
 * WYSIWYG widget plugin main block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Widget_Block_Adminhtml_Widget extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_blockGroup = 'widget';
        $this->_controller = 'adminhtml';
        $this->_mode = 'widget';
        $this->_headerText = $this->helper('widget')->__('Widget Insertion');

        $this->removeButton(self::BUTTON_TYPE_RESET);
        $this->removeButton(self::BUTTON_TYPE_BACK);
        $this->_updateButton(self::BUTTON_TYPE_SAVE, 'label', $this->helper('widget')->__('Insert Widget'));
        $this->_updateButton(self::BUTTON_TYPE_SAVE, 'class', 'add-widget');
        $this->_updateButton(self::BUTTON_TYPE_SAVE, 'id', 'insert_button');
        $this->_updateButton(self::BUTTON_TYPE_SAVE, 'onclick', 'wWidget.insertWidget()');

        $this->_formScripts[] = 'wWidget = new WysiwygWidget.Widget('
            . '"widget_options_form", "select_widget_type", "widget_options", "'
            . $this->getUrl('*/*/loadOptions') . '", "' . $this->getRequest()->getParam('widget_target_id') . '");';
    }
}
