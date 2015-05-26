<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml base helper
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_ADMINHTML_ROUTER_FRONTNAME   = 'admin/routers/adminhtml/args/frontName';
    const XML_PATH_USE_CUSTOM_ADMIN_URL         = 'default/admin/url/use_custom';
    const XML_PATH_USE_CUSTOM_ADMIN_PATH        = 'default/admin/url/use_custom_path';
    const XML_PATH_CUSTOM_ADMIN_PATH            = 'default/admin/url/custom_path';

    protected $_pageHelpUrl;

    public function getPageHelpUrl()
    {
        if (!$this->_pageHelpUrl) {
            $this->setPageHelpUrl();
        }
        return $this->_pageHelpUrl;
    }

    public function setPageHelpUrl($url=null)
    {
        if (is_null($url)) {
            $request = Mage::app()->getRequest();
            $frontModule = $request->getControllerModule();
            if (!$frontModule) {
                $frontName = $request->getModuleName();
                $router = Mage::app()->getFrontController()->getRouterByFrontName($frontName);

                $frontModule = $router->getModuleByFrontName($frontName);
                if (is_array($frontModule)) {
                    $frontModule = $frontModule[0];
                }
            }
            $url = 'http://www.magentocommerce.com/gethelp/';
            $url.= Mage::app()->getLocale()->getLocaleCode().'/';
            $url.= $frontModule.'/';
            $url.= $request->getControllerName().'/';
            $url.= $request->getActionName().'/';

            $this->_pageHelpUrl = $url;
        }
        $this->_pageHelpUrl = $url;

        return $this;
    }

    public function addPageHelpUrl($suffix)
    {
        $this->_pageHelpUrl = $this->getPageHelpUrl().$suffix;
        return $this;
    }

    public static function getUrl($route='', $params=array())
    {
        return Mage::getModel('adminhtml/url')->getUrl($route, $params);
    }

//    public function getCurrentUserId()
//    {
//        return Mage::getSingleton('admin/session')->getUser()->getId();
//    }
    public function getCurrentUserId()
    {
        if (Mage::getSingleton('admin/session')->getUser()) {
            return Mage::getSingleton('admin/session')->getUser()->getId();
        }
        return false;
    }

    /**
     * Decode filter string
     *
     * @param string $filterString
     * @return data
     */
    public function prepareFilterString($filterString)
    {
        $data = array();
        $filterString = base64_decode($filterString);
        parse_str($filterString, $data);
        array_walk_recursive($data, array($this, 'decodeFilter'));
        return $data;
    }

    /**
     * Decode URL encoded filter value recursive callback method
     *
     * @param string $value
     */
    public function decodeFilter(&$value)
    {
        $value = trim(rawurldecode($value));
    }
}
