<?php
/**
 * Adminhtml header block
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Page_Header extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('page/header.phtml');
    }

    public function getHomeLink()
    {
        return $this->getUrl('adminhtml');
    }

    public function getUser()
    {
        return Mage::getSingleton('admin/session')->getUser();
    }

    public function getLogoutLink()
    {
        return $this->getUrl('adminhtml/index/logout');
    }

    /**
     * Check if noscript notice should be displayed
     *
     * @return bool
     */
    public function displayNoscriptNotice()
    {
        return Mage::getStoreConfig('web/browser_capabilities/javascript');
    }
}
