<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml header block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Page_Head extends Mage_Page_Block_Html_Head
{
    /**
     * @return string
     */
    protected function _getUrlModelClass()
    {
        return 'adminhtml/url';
    }

    /**
     * Retrieve Session Form Key
     *
     * @return string
     */
    public function getFormKey()
    {
        return Mage::getSingleton('core/session')->getFormKey();
    }

    /**
     * Retrieve Timeout Delay from Config
     *
     * @return int
     * @since 19.4.18 / 20.0.16
     */
    public function getLoadingTimeout()
    {
        return Mage::getStoreConfigAsInt('admin/design/loading_timeout');
    }
}
