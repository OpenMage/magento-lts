<?php
/**
 * Adminhtml Widget Tab Interface
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
interface Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel();

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle();

    /**
     * Can show tab in tabs
     *
     * @return bool
     */
    public function canShowTab();

    /**
     * Tab is hidden
     *
     * @return bool
     */
    public function isHidden();
}
