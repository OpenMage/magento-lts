<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Widget
 */

/**
 * Widget Block Interface
 *
 * @package    Mage_Widget
 */
interface Mage_Widget_Block_Interface
{
    /**
     * Produce and return widget's html output
     *
     * @return string
     */
    public function toHtml();

    /**
     * Add data to the widget.
     * Retains previous data in the widget.
     *
     * @return Mage_Widget_Block_Interface
     */
    public function addData(array $arr);

    /**
     * Overwrite data in the widget.
     *
     * $key can be string or array.
     * If $key is string, the attribute value will be overwritten by $value.
     * If $key is an array, it will overwrite all the data in the widget.
     *
     * @param string|array $key
     * @param mixed $value
     * @return Varien_Object
     */
    public function setData($key, $value = null);
}
