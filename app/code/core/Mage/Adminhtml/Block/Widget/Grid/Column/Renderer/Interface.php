<?php
/**
 * Adminhtml grid item renderer interface
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
interface Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Interface
{
    /**
     * Set column for renderer
     *
     * @abstract
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return void
     */
    public function setColumn($column);

    /**
     * Returns row associated with the renderer
     *
     * @abstract
     * @return Mage_Adminhtml_Block_Widget_Grid_Column
     */
    public function getColumn();

    /**
     * Renders grid column
     */
    public function render(Varien_Object $row);
}
