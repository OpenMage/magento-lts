<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Cms
 */

/**
 * Cms page content block
 *
 * @package    Mage_Cms
 *
 * @method int getPageId()
 */
class Mage_Cms_Block_Page extends Mage_Core_Block_Abstract
{
    /**
     * Retrieve Page instance
     *
     * @return Mage_Cms_Model_Page
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getPage()
    {
        if (!$this->hasData('page')) {
            if ($this->getPageId()) {
                $page = Mage::getModel('cms/page')
                    ->setStoreId(Mage::app()->getStore()->getId())
                    ->load($this->getPageId(), 'identifier');
            } else {
                $page = Mage::getSingleton('cms/page');
            }

            $this->addModelTags($page);
            $this->setData('page', $page);
        }

        return $this->getDataByKey('page');
    }

    /**
     * @inheritDoc
     * @throws Mage_Core_Model_Store_Exception
     */
    #[Override]
    protected function _prepareLayout()
    {
        $page = $this->getPage();
        $helper = Mage::helper('cms/page');
        $breadcrumbsArray = [];
        $breadcrumbs = null;

        // show breadcrumbs
        if (Mage::getStoreConfig('web/default/show_cms_breadcrumbs')
            && ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs'))
            && ($page->getIdentifier() !== $helper->getIdentifierFromConfigPath(Mage_Cms_Helper_Page::XML_PATH_HOME_PAGE))
            && ($page->getIdentifier() !== $helper->getIdentifierFromConfigPath(Mage_Cms_Helper_Page::XML_PATH_NO_ROUTE_PAGE))
        ) {
            $breadcrumbsArray[] = [
                'crumbName' => 'home',
                'crumbInfo' => [
                    'label' => Mage::helper('cms')->__('Home'),
                    'title' => Mage::helper('cms')->__('Go to Home Page'),
                    'link'  => Mage::getBaseUrl(),
                ],
            ];
            $breadcrumbsArray[] = [
                'crumbName' => 'cms_page',
                'crumbInfo' => [
                    'label' => $page->getTitle(),
                    'title' => $page->getTitle(),
                ],
            ];
            $breadcrumbsObject = new Varien_Object();
            $breadcrumbsObject->setCrumbs($breadcrumbsArray);

            Mage::dispatchEvent('cms_generate_breadcrumbs', ['breadcrumbs' => $breadcrumbsObject]);

            if ($breadcrumbs instanceof Mage_Page_Block_Html_Breadcrumbs) {
                foreach ($breadcrumbsObject->getCrumbs() as $breadcrumbsItem) {
                    $breadcrumbs->addCrumb($breadcrumbsItem['crumbName'], $breadcrumbsItem['crumbInfo']);
                }
            }
        }

        /** @var Mage_Page_Block_Html $root */
        $root = $this->getLayout()->getBlock('root');
        if ($root) {
            $root->addBodyClass('cms-' . $page->getIdentifier());
        }

        /** @var Mage_Page_Block_Html_Head $head */
        $head = $this->getLayout()->getBlock('head');
        if ($head) {
            $head->setTitle($page->getTitle());
            $head->setKeywords($page->getMetaKeywords());
            $head->setDescription($page->getMetaDescription());

            // Add canonical tag if enabled
            if (Mage::helper('cms')->canUseCanonicalTag()) {
                $canonicalUrl = $this->getCanonicalUrl($page);
                if ($canonicalUrl) {
                    $head->addLinkRel('canonical', $canonicalUrl);
                }
            }
        }

        return parent::_prepareLayout();
    }

    /**
     * Prepare HTML content
     *
     * @return string
     * @throws Exception
     * @throws Mage_Core_Model_Store_Exception
     */
    #[Override]
    protected function _toHtml()
    {
        /** @var Mage_Cms_Helper_Data $helper */
        $helper = Mage::helper('cms');
        $processor = $helper->getPageTemplateProcessor();
        $html = $processor->filter($this->getPage()->getContent());
        return $this->getMessagesBlock()->toHtml() . $html;
    }

    /**
     * Get canonical URL for CMS page
     */
    protected function getCanonicalUrl(Mage_Cms_Model_Page $page): ?string
    {
        if (!$page->getId()) {
            return null;
        }

        // Check if page is active
        if (!$page->getIsActive()) {
            return null;
        }

        // Get the page identifier
        $identifier = $page->getIdentifier();
        $helper = Mage::helper('cms/page');

        // Handle special pages differently
        $homePageId = $helper->getIdentifierFromConfigPath(Mage_Cms_Helper_Page::XML_PATH_HOME_PAGE);
        $noRoutePageId = $helper->getIdentifierFromConfigPath(Mage_Cms_Helper_Page::XML_PATH_NO_ROUTE_PAGE);
        $noCookiesPageId = $helper->getIdentifierFromConfigPath(Mage_Cms_Helper_Page::XML_PATH_NO_COOKIES_PAGE);

        // For homepage, use base URL
        if ($identifier === $homePageId) {
            return Mage::getBaseUrl();
        }

        // For special pages that shouldn't have canonical tags
        if (in_array($identifier, [$noRoutePageId, $noCookiesPageId])) {
            return null;
        }

        // For regular CMS pages, use the standard CMS page URL
        return $this->getUrl('', ['_direct' => $identifier, '_nosid' => true]);
    }
}
