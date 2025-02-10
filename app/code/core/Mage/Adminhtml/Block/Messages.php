<?php
/**
 * Adminhtml messages block
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Messages extends Mage_Core_Block_Messages
{
    /**
     * @return string
     */
    protected function _getUrlModelClass()
    {
        return 'adminhtml/url';
    }
}
