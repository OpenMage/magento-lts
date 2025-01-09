<?php

/**
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml Rss Controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Rss_CatalogController extends Mage_Adminhtml_Controller_Rss_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _isAllowed()
    {
        $path = '';
        $action = strtolower($this->getRequest()->getActionName());
        if ($action == 'review') {
            $path = 'catalog/reviews_ratings';
        } elseif ($action == 'notifystock') {
            $path = 'catalog/products';
        }
        return Mage::getSingleton('admin/session')->isAllowed($path);
    }

    public function notifystockAction()
    {
        if ($this->checkFeedEnable('admin_catalog/notifystock')) {
            $this->loadLayout(false);
            $this->renderLayout();
        }
    }

    public function reviewAction()
    {
        if ($this->checkFeedEnable('admin_catalog/review')) {
            $this->loadLayout(false);
            $this->renderLayout();
        }
    }
}
