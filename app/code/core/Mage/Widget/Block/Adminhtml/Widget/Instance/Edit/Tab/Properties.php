<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Widget
 */

/**
 * Widget Instance Properties tab block
 *
 * @package    Mage_Widget
 *
 * @method $this setWidgetType(string $value)
 * @method $this setWidgetValues(array $value)
 */
class Mage_Widget_Block_Adminhtml_Widget_Instance_Edit_Tab_Properties extends Mage_Widget_Block_Adminhtml_Widget_Options implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('widget')->__('Widget Options');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('widget')->__('Widget Options');
    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return bool
     */
    public function canShowTab()
    {
        return $this->getWidgetInstance()->isCompleteToCreate();
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return false
     */
    public function isHidden()
    {
        return false;
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
     * Prepare block children and data.
     * Set widget type and widget parameters if available
     *
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        $this->setWidgetType($this->getWidgetInstance()->getType())
            ->setWidgetValues($this->getWidgetInstance()->getWidgetParameters());
        return parent::_prepareLayout();
    }

    /**
     * Add field to Options form based on option configuration
     *
     * @param  Varien_Object                           $parameter
     * @return false|Varien_Data_Form_Element_Abstract
     */
    protected function _addField($parameter)
    {
        if ($parameter->getKey() != 'template') {
            return parent::_addField($parameter);
        }

        return false;
    }
}
