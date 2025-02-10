<?php
/**
 * Adminhtml review grid filter by type
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Review_Grid_Filter_Type extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select
{
    /**
     * @return array
     */
    protected function _getOptions()
    {
        return [
            ['label' => '', 'value' => ''],
            ['label' => Mage::helper('review')->__('Administrator'), 'value' => 1],
            ['label' => Mage::helper('review')->__('Customer'), 'value' => 2],
            ['label' => Mage::helper('review')->__('Guest'), 'value' => 3],
        ];
    }

    /**
     * @return int
     */
    public function getCondition()
    {
        if ($this->getValue() == 1) {
            return 1;
        } elseif ($this->getValue() == 2) {
            return 2;
        } else {
            return 3;
        }
    }
}
