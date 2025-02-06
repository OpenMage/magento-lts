<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
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
