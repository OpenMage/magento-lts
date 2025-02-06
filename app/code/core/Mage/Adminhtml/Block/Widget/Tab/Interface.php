<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml Widget Tab Interface
 *
 * @category   Mage
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
