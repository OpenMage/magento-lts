<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Catalog
 */

/**
 * SEO sitemap controller
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
class Mage_Catalog_Seo_SitemapController extends Mage_Core_Controller_Front_Action
{
    /**
     * Check if SEO sitemap is enabled in configuration
     *
     * @return $this
     */
    public function preDispatch()
    {
        parent::preDispatch();
        if (!Mage::getStoreConfig('catalog/seo/site_map')) {
            $this->_redirect('noroute');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
        return $this;
    }

    /**
     * Display categories listing
     *
     */
    public function categoryAction()
    {
        $update = $this->getLayout()->getUpdate();
        $update->addHandle('default');
        $this->addActionLayoutHandles();
        if (Mage::helper('catalog/map')->getIsUseCategoryTreeMode()) {
            $update->addHandle(strtolower($this->getFullActionName()) . '_tree');
        }
        $this->loadLayoutUpdates();
        $this->generateLayoutXml()->generateLayoutBlocks();
        $this->renderLayout();
    }

    /**
     * Display products listing
     *
     */
    public function productAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
}
