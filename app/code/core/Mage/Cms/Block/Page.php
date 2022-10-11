<?php
/**
 * OpenMage
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
 * @category    Mage
 * @package     Mage_Cms
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Cms page content block
 *
 * @method int getPageId()
 *
 * @category   Mage
 * @package    Mage_Cms
 * @author     Magento Core Team <core@magentocommerce.com>
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
        return $this->getData('page');
    }

    /**
     * @inheritDoc
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function _prepareLayout()
    {
        $page = $this->getPage();
        $breadcrumbsArray = [];
        $breadcrumbs = null;

        // show breadcrumbs
        if (Mage::getStoreConfig('web/default/show_cms_breadcrumbs')
            && ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs'))
            && ($page->getIdentifier()!==Mage::getStoreConfig('web/default/cms_home_page'))
            && ($page->getIdentifier()!==Mage::getStoreConfig('web/default/cms_no_route'))) {
            $breadcrumbsArray[] = [
                'crumbName' => 'home',
                'crumbInfo' => [
                    'label' => Mage::helper('cms')->__('Home'),
                    'title' => Mage::helper('cms')->__('Go to Home Page'),
                    'link'  => Mage::getBaseUrl()
                ]
            ];
            $breadcrumbsArray[] = [
                'crumbName' => 'cms_page',
                'crumbInfo' => [
                    'label' => $page->getTitle(),
                    'title' => $page->getTitle()
                ]
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
            $root->addBodyClass('cms-'.$page->getIdentifier());
        }

        /** @var Mage_Page_Block_Html_Head $head */
        $head = $this->getLayout()->getBlock('head');
        if ($head) {
            $head->setTitle($page->getTitle());
            $head->setKeywords($page->getMetaKeywords());
            $head->setDescription($page->getMetaDescription());
        }

        return parent::_prepareLayout();
    }

    /**
     * Prepare HTML content
     *
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     * @throws Exception
     */
    protected function _toHtml()
    {
        /** @var Mage_Cms_Helper_Data $helper */
        $helper = Mage::helper('cms');
        $processor = $helper->getPageTemplateProcessor();
        $html = $processor->filter($this->getPage()->getContent());
        $html = $this->getMessagesBlock()->toHtml() . $html;
        return $html;
    }
}
