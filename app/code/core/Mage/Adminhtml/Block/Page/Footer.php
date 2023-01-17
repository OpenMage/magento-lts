<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml footer block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Page_Footer extends Mage_Adminhtml_Block_Template
{
    public const LOCALE_CACHE_LIFETIME = 7200;
    public const LOCALE_CACHE_KEY      = 'footer_locale';
    public const LOCALE_CACHE_TAG      = 'adminhtml';

    protected function _construct()
    {
        $this->setTemplate('page/footer.phtml');
        $this->setShowProfiler(true);
    }

    /**
     * @return string
     */
    public function getChangeLocaleUrl()
    {
        return $this->getUrl('adminhtml/index/changeLocale');
    }

    /**
     * @return string
     */
    public function getUrlForReferer()
    {
        return $this->getUrlEncoded('*/*/*', ['_current' => true]);
    }

    /**
     * @return string
     */
    public function getRefererParamName()
    {
        return Mage_Core_Controller_Varien_Action::PARAM_NAME_URL_ENCODED;
    }

    /**
     * @return string
     */
    public function getLanguageSelect()
    {
        $locale  = Mage::app()->getLocale();
        $cacheId = self::LOCALE_CACHE_KEY . $locale->getLocaleCode();
        $html    = Mage::app()->loadCache($cacheId);

        if (!$html) {
            $html = $this->getLayout()->createBlock('adminhtml/html_select')
                ->setName('locale')
                ->setId('interface_locale')
                ->setTitle(Mage::helper('page')->__('Interface Language'))
                ->setExtraParams('style="width:200px"')
                ->setValue($locale->getLocaleCode())
                ->setOptions($locale->getTranslatedOptionLocales())
                ->getHtml();
            Mage::app()->saveCache($html, $cacheId, [self::LOCALE_CACHE_TAG], self::LOCALE_CACHE_LIFETIME);
        }

        return $html;
    }

    /**
     * @param string $url
     * @return $this
     * @deprecated see setReportIssuesUrl()
     */
    public function setBugreportUrl(string $url)
    {
        return $this->setReportIssuesUrl($url);
    }

    /**
     * @return string
     * @deprecated see getReportIssuesUrl()
     */
    public function getBugreportUrl(): string
    {
        return $this->getReportIssuesUrl();
    }

    /**
     * @param string $url
     * @return $this
     */
    public function setReportIssuesUrl(string $url)
    {
        return $this->setData('report_issues_url', $url);
    }

    /**
     * @return string
     */
    public function getReportIssuesUrl(): string
    {
        return (string) $this->_getData('report_issues_url');
    }

    /**
     * @param string $url
     * @return $this
     * @deprecated see setOpenMageProjectUrl()
     */
    public function setConnectWithMagentoUrl(string $url)
    {
        return $this->setOpenMageProjectUrl($url);
    }

    /**
     * @return string
     * @deprecated see getOpenMageProjectUrl()
     */
    public function getConnectWithMagentoUrl(): string
    {
        return $this->getOpenMageProjectUrl();
    }

    /**
     * @param string $url
     * @return $this
     */
    public function setOpenMageProjectUrl(string $url)
    {
        return $this->setData('openmage_project_url', $url);
    }

    /**
     * @return string
     */
    public function getOpenMageProjectUrl(): string
    {
        return (string) $this->_getData('openmage_project_url');
    }
}
