<?php
/**
 * Massaction grid column filter
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Massaction extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Checkbox
{
    public function getCondition()
    {
        if ($this->getValue()) {
            return ['in' => ($this->getColumn()->getSelected() ? $this->getColumn()->getSelected() : [0])];
        } else {
            return ['nin' => ($this->getColumn()->getSelected() ? $this->getColumn()->getSelected() : [0])];
        }
    }
}
