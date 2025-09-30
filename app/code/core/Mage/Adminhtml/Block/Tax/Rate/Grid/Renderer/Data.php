<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml grid item renderer number
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Tax_Rate_Grid_Renderer_Data extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    protected function _getValue(Varien_Object $row)
    {
        $data = parent::_getValue($row);
        if ((int) $data == $data) {
            return (string) number_format((float) $data, 2);
        }
        if (!is_null($data)) {
            return $data * 1;
        }
        return $this->getColumn()->getDefault();
    }
}
