<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml base helper
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Helper_Data extends Mage_Adminhtml_Helper_Help_Mapping
{
    public const XML_PATH_ADMINHTML_ROUTER_FRONTNAME   = 'admin/routers/adminhtml/args/frontName';

    public const XML_PATH_USE_CUSTOM_ADMIN_URL         = 'default/admin/url/use_custom';

    public const XML_PATH_CUSTOM_ADMIN_URL             = 'default/admin/url/custom';

    public const XML_PATH_USE_CUSTOM_ADMIN_PATH        = 'default/admin/url/use_custom_path';

    public const XML_PATH_CUSTOM_ADMIN_PATH            = 'default/admin/url/custom_path';

    public const XML_PATH_ADMINHTML_SECURITY_USE_FORM_KEY = 'admin/security/use_form_key';

    protected $_moduleName = 'Mage_Adminhtml';

    /** @deprecated */
    protected $_pageHelpUrl;

    /**
     * Get mapped help pages url
     *
     * @param null|string $url
     * @param null|string $suffix
     * @return mixed
     * @deprecated
     */
    public function getPageHelpUrl($url = null, $suffix = null)
    {
        if (!$this->_pageHelpUrl) {
            $this->setPageHelpUrl($url, $suffix);
        }

        return $this->_pageHelpUrl;
    }

    /**
     * Set help page url
     *
     * @param null|string $url
     * @param null|string $suffix
     * @return $this
     * @deprecated
     */
    public function setPageHelpUrl($url = null, $suffix = null)
    {
        $this->_pageHelpUrl = $url;
        return $this;
    }

    /**
     * Add suffix for help page url
     *
     * @param string $suffix
     * @return $this
     * @deprecated
     */
    public function addPageHelpUrl($suffix)
    {
        $this->_pageHelpUrl = $this->getPageHelpUrl(null, $suffix);
        return $this;
    }

    /**
     * @param string $route
     * @param array $params
     * @return string
     */
    public static function getUrl($route = '', $params = [])
    {
        return Mage::getModel('adminhtml/url')->getUrl($route, $params);
    }

    /**
     * Get custom admin URL (validated and normalized when saved via admin panel)
     */
    public static function getCustomAdminUrl(): string
    {
        $config = Mage::getConfig();

        // Check if use custom admin URL is enabled
        $useCustom = (int) $config->getNode(self::XML_PATH_USE_CUSTOM_ADMIN_URL);
        return $useCustom
            ? (string) $config->getNode(self::XML_PATH_CUSTOM_ADMIN_URL)
            : '';
    }

    /**
     * @return false|int
     */
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
     * @return array
     */
    public function prepareFilterString($filterString)
    {
        $data = [];
        $filterString = base64_decode($filterString);
        parse_str($filterString, $data);
        array_walk_recursive($data, [$this, 'decodeFilter']);
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

    /**
     * Check if enabled "Add Secret Key to URLs" functionality
     *
     * @return bool
     */
    public function isEnabledSecurityKeyUrl()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ADMINHTML_SECURITY_USE_FORM_KEY);
    }
}
