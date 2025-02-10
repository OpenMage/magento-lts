<?php
/**
 * Grid column filter interface
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
interface Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Interface
{
    public function getColumn();
    public function setColumn($column);
    public function getHtml();
}
