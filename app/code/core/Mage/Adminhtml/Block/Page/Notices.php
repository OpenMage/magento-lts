<?php
/**
 * Adminhtml header notices block
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Page_Notices extends Mage_Adminhtml_Block_Template
{
    /**
     * Check if noscript notice should be displayed
     *
     * @return bool
     */
    public function displayNoscriptNotice()
    {
        return Mage::getStoreConfig('web/browser_capabilities/javascript');
    }

    /**
     * Check if demo store notice should be displayed
     *
     * @return bool
     */
    public function displayDemoNotice()
    {
        return Mage::getStoreConfig('design/head/demonotice');
    }
}
