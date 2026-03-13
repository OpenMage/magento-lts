<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Widget
 */

/**
 * Widget Instance edit container
 *
 * @package    Mage_Widget
 */
class Mage_Widget_Block_Adminhtml_Widget_Instance_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_objectId = 'instance_id';
        $this->_blockGroup = 'widget';
        $this->_controller = 'adminhtml_widget_instance';
    }

    /**
     * Getter
     *
     * @return Mage_Widget_Model_Widget_Instance
     */
    public function getWidgetInstance()
    {
        return Mage::registry('current_widget_instance');
    }

    /**
     * Prepare layout.
     * Adding save_and_continue button
     *
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        if ($this->getWidgetInstance()->isCompleteToCreate()) {
            $this->_addButton(
                'save_and_edit_button',
                [
                    'label'     => Mage::helper('widget')->__('Save and Continue Edit'),
                    'class'     => 'save continue',
                    'onclick'   => 'saveAndContinueEdit()',
                ],
                100,
            );
        } else {
            $this->removeButton('save');
        }

        return parent::_prepareLayout();
    }

    /**
     * Return translated header text depending on creating/editing action
     *
     * @return string
     */
    public function getHeaderText()
    {
        if ($this->getWidgetInstance()->getId()) {
            return Mage::helper('widget')->__('Widget "%s"', $this->escapeHtml($this->getWidgetInstance()->getTitle()));
        }

        return Mage::helper('widget')->__('New Widget Instance');
    }

    /**
     * Return validation url for edit form
     *
     * @return string
     */
    public function getValidationUrl()
    {
        return $this->getUrl('*/*/validate', ['_current' => true]);
    }

    /**
     * Return save url for edit form
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', ['_current' => true, 'back' => null]);
    }
}
