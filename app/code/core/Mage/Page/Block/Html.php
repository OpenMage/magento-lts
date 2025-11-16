<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Page
 */

/**
 * Html page block
 *
 * @package    Mage_Page
 *
 * @method bool getIsHandle()
 * @method string getLayoutCode()
 * @method $this setBodyClass(string $value)
 */
class Mage_Page_Block_Html extends Mage_Core_Block_Template
{
    protected $_urls = [];

    protected $_title = '';

    public function __construct()
    {
        parent::__construct();
        $this->_urls = [
            'base'      => Mage::getBaseUrl('web'),
            'baseSecure' => Mage::getBaseUrl('web', true),
            'current'   => $this->getRequest()->getRequestUri(),
        ];

        $action = Mage::app()->getFrontController()->getAction();
        if ($action) {
            $this->addBodyClass($action->getFullActionName('-'));
        }

        $this->_beforeCacheUrl();
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->_urls['base'];
    }

    /**
     * @return string
     */
    public function getBaseSecureUrl()
    {
        return $this->_urls['baseSecure'];
    }

    /**
     * @return string
     */
    public function getCurrentUrl()
    {
        return $this->_urls['current'];
    }

    /**
     *  Print Logo URL (Conf -> Sales -> Invoice and Packing Slip Design)
     *
     *  @return   string
     */
    public function getPrintLogoUrl()
    {
        // load html logo
        $logo = Mage::getStoreConfig('sales/identity/logo_html');
        if (!empty($logo)) {
            $logo = 'sales/store/logo_html/' . $logo;
        }

        // load default logo
        if (empty($logo)) {
            $logo = Mage::getStoreConfig('sales/identity/logo');
            if (!empty($logo)) {
                // prevent tiff format displaying in html
                if (strtolower(substr($logo, -5)) === '.tiff' || strtolower(substr($logo, -4)) === '.tif') {
                    $logo = '';
                } else {
                    $logo = 'sales/store/logo/' . $logo;
                }
            }
        }

        // build url
        if (!empty($logo)) {
            $logo = Mage::getStoreConfig(Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_MEDIA_URL) . $logo;
        } else {
            $logo = '';
        }

        return $logo;
    }

    /**
     * @return string
     */
    public function getPrintLogoText()
    {
        return Mage::getStoreConfig('sales/identity/address');
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setHeaderTitle($title)
    {
        $this->_title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getHeaderTitle()
    {
        return $this->_title;
    }

    /**
     * Add CSS class to page body tag
     *
     * @param string $className
     * @return $this
     */
    public function addBodyClass($className)
    {
        $className = preg_replace('#[^a-z0-9]+#', '-', strtolower($className));
        $class = $this->getBodyClass() ? $this->getBodyClass() . ' ' . $className : $className;
        $this->setBodyClass($class);
        return $this;
    }

    /**
     * @return string
     */
    public function getLang()
    {
        if (!$this->hasData('lang')) {
            $this->setData('lang', substr(Mage::app()->getLocale()->getLocaleCode(), 0, 2));
        }

        return $this->getData('lang');
    }

    /**
     * @param string $theme
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function setTheme($theme)
    {
        $arr = explode('/', $theme);
        if (isset($arr[1])) {
            Mage::getDesign()->setPackageName($arr[0])->setTheme($arr[1]);
        } else {
            Mage::getDesign()->setTheme($theme);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getBodyClass()
    {
        return $this->_getData('body_class');
    }

    /**
     * @return string
     */
    public function getAbsoluteFooter()
    {
        return Mage::getStoreConfig('design/footer/absolute_footer');
    }

    /**
     * Processing block html after rendering
     *
     * @param   string $html
     * @return  string
     */
    protected function _afterToHtml($html)
    {
        return $this->_afterCacheUrl($html);
    }
}
