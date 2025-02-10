<?php
/**
 * Adminhtml grid item renderer concat
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
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
