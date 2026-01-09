<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Cms
 */

/**
 * Cms index controller
 *
 * @package    Mage_Cms
 */
class Mage_Cms_IndexController extends Mage_Core_Controller_Front_Action
{
    /**
     * Renders CMS Home page
     *
     * @param  string              $coreRoute
     * @return void
     * @throws Mage_Core_Exception
     */
    public function indexAction($coreRoute = null)
    {
        $pageId = Mage::getStoreConfig(Mage_Cms_Helper_Page::XML_PATH_HOME_PAGE);
        if (!Mage::helper('cms/page')->renderPage($this, $pageId)) {
            $this->_forward('defaultIndex');
        }
    }

    /**
     * Default index action (with 404 Not Found headers)
     * Used if default page don't configure or available
     *
     * @return void
     * @throws Mage_Core_Exception
     * @throws Mage_Core_Model_Store_Exception
     */
    public function defaultIndexAction()
    {
        $this->getResponse()->setHeader('HTTP/1.1', '404 Not Found');
        $this->getResponse()->setHeader('Status', '404 File not found');

        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Render CMS 404 Not found page
     *
     * @param  string              $coreRoute
     * @return void
     * @throws Mage_Core_Exception
     */
    public function norouteAction($coreRoute = null)
    {
        $this->getResponse()->setHeader('HTTP/1.1', '404 Not Found');
        $this->getResponse()->setHeader('Status', '404 File not found');

        $pageId = Mage::getStoreConfig(Mage_Cms_Helper_Page::XML_PATH_NO_ROUTE_PAGE);
        if (!Mage::helper('cms/page')->renderPage($this, $pageId)) {
            $this->_forward('defaultNoRoute');
        }
    }

    /**
     * Default no route page action
     * Used if no route page don't configure or available
     *
     * @return void
     * @throws Mage_Core_Exception
     * @throws Mage_Core_Model_Store_Exception
     */
    public function defaultNoRouteAction()
    {
        $this->getResponse()->setHeader('HTTP/1.1', '404 Not Found');
        $this->getResponse()->setHeader('Status', '404 File not found');

        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Render Disable cookies page
     *
     * @return void
     * @throws Mage_Core_Exception
     */
    public function noCookiesAction()
    {
        $pageId = Mage::getStoreConfig(Mage_Cms_Helper_Page::XML_PATH_NO_COOKIES_PAGE);
        if (!Mage::helper('cms/page')->renderPage($this, $pageId)) {
            $this->_forward('defaultNoCookies');
        }
    }

    /**
     * Default no cookies page action
     * Used if no cookies page don't configure or available
     *
     * @return void
     * @throws Mage_Core_Exception
     * @throws Mage_Core_Model_Store_Exception
     */
    public function defaultNoCookiesAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
}
