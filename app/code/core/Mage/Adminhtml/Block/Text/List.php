<?php
/**
 * Adminhtml text list block
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
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
