<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Massaction grid column filter
 *
 * @category   Mage
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
