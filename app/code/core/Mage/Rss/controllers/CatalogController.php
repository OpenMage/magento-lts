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
 * @package     Mage_Rss
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer reviews controller
 *
 * @category   Mage
 * @package    Mage_Rss
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Rss_CatalogController extends Mage_Rss_Controller_Abstract
{
    public function newAction()
    {
        if ($this->checkFeedEnable('catalog/new')) {
            $this->loadLayout(false);
            $this->renderLayout();
        }
    }

    public function specialAction()
    {
        if ($this->checkFeedEnable('catalog/special')) {
            $this->loadLayout(false);
            $this->renderLayout();
        }
    }

    public function salesruleAction()
    {
        if ($this->checkFeedEnable('catalog/salesrule')) {
            $this->loadLayout(false);
            $this->renderLayout();
        }
    }

    public function tagAction()
    {
        if ($this->isFeedEnable('catalog/tag')) {
            $tagName = urldecode($this->getRequest()->getParam('tagName'));
            $tagModel = Mage::getModel('tag/tag');
            $tagModel->loadByName($tagName);
            if ($tagModel->getId() && $tagModel->getStatus()==$tagModel->getApprovedStatus()) {
                Mage::register('tag_model', $tagModel);
                $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
                $this->loadLayout(false);
                $this->renderLayout();
                return;
            }
        }
        $this->_forward('nofeed', 'index', 'rss');
    }

    public function notifystockAction()
    {
        if ($this->checkFeedEnable('catalog/notifystock')) {
            $this->loadLayout(false);
            $this->renderLayout();
        }
    }

    public function reviewAction()
    {
        if ($this->checkFeedEnable('catalog/review')) {
            $this->loadLayout(false);
            $this->renderLayout();
        }
    }

    public function categoryAction()
    {
        if ($this->checkFeedEnable('catalog/category')) {
            $this->loadLayout(false);
            $this->renderLayout();
        }
    }

    /**
     * Controller predispatch method to change area for some specific action.
     *
     * @return Mage_Rss_CatalogController
     */
    public function preDispatch()
    {
        $action = strtolower($this->getRequest()->getActionName());
        if ($action == 'notifystock' && $this->isFeedEnable('catalog/notifystock')) {
            $this->_currentArea = 'adminhtml';
            Mage::helper('rss')->authAdmin('catalog/products');
        }
        if ($action == 'review' && $this->isFeedEnable('catalog/review')) {
            $this->_currentArea = 'adminhtml';
            Mage::helper('rss')->authAdmin('catalog/reviews_ratings');
        }
        return parent::preDispatch();
    }
}
