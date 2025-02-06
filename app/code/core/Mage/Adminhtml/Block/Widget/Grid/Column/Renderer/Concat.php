<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml grid item renderer concat
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Concat extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $dataArr = [];
        foreach ($this->getColumn()->getIndex() as $index) {
            if ($data = $row->getData($index)) {
                $dataArr[] = $data;
            }
        }
        // TODO run column type renderer
        return implode($this->getColumn()->getSeparator(), $dataArr);
    }
}
