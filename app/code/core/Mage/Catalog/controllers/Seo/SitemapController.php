<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
