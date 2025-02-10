<?php
/**
 * Adminhtml Tax Class Edit
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Tax_Class_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId    = 'id';
        $this->_controller  = 'tax_class';

        parent::__construct();

        $this->_updateButton('save', 'label', Mage::helper('tax')->__('Save Class'));
        $this->_updateButton('delete', 'label', Mage::helper('tax')->__('Delete Class'));
    }

    /**
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('tax_class')->getId()) {
            return Mage::helper('tax')->__("Edit Class '%s'", $this->escapeHtml(Mage::registry('tax_class')->getClassName()));
        }
        return Mage::helper('tax')->__('New Class');
    }

    /**
     * @param string $classType
     * @return $this
     */
    public function setClassType($classType)
    {
        $this->getChild('form')->setClassType($classType);
        return $this;
    }
}
