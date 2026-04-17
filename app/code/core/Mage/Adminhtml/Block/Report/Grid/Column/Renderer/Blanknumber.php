<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml grid item renderer number or blank line
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Report_Grid_Column_Renderer_Blanknumber extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Number
{
    protected function _getValue(Varien_Object $row)
    {
        $data = parent::_getValue($row);
        if (!is_null($data)) {
            $value = $data * 1;
            return $value ? $value : ''; // fixed for showing blank cell in grid
            /**
             * @todo may be bug in i.e. needs to be fixed
             */
        }

        return $this->getColumn()->getDefault();
    }
}
