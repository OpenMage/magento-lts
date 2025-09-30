<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml HTML select element block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Html_Select extends Mage_Core_Block_Html_Select
{
    /**
     * @return string
     */
    protected function _getUrlModelClass()
    {
        return 'adminhtml/url';
    }
}
