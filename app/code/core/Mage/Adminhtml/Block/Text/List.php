<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml text list block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Text_List extends Mage_Core_Block_Text_List
{
    /**
     * @return string
     */
    protected function _getUrlModelClass()
    {
        return 'adminhtml/url';
    }
}
