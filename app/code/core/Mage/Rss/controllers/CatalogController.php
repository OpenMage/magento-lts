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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Rss
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer reviews controller
 *
 * @category   Mage
 * @package    Mage_Rss
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Rss_CatalogController extends Mage_Core_Controller_Front_Action
{
    protected function isFeedEnable($code)
    {
        return Mage::getStoreConfig('rss/catalog/'.$code);
    }

    protected function checkFeedEnable($code)
    {
        if ($this->isFeedEnable($code)) {
            $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
            return true;
        } else {
            $this->getResponse()->setHeader('HTTP/1.1','404 Not Found');
            $this->getResponse()->setHeader('Status','404 File not found');
            $this->_forward('nofeed','index','rss');
            return false;
        }
    }

    public function newAction()
    {
        $this->checkFeedEnable('new');
        $this->loadLayout(false);
        $this->renderLayout();
    }

    public function specialAction()
    {
        $this->checkFeedEnable('special');
        $this->loadLayout(false);
        $this->renderLayout();
    }

    public function salesruleAction()
    {
        $this->checkFeedEnable('salesrule');
        $this->loadLayout(false);
        $this->renderLayout();
    }

    public function tagAction()
    {
        if ($this->checkFeedEnable('tag')) {
            $tagName = $this->getRequest()->getParam('tagName');
            $tagModel = Mage::getModel('tag/tag');
            $tagModel->loadByName($tagName);
            if ($tagModel->getId() && $tagModel->getStatus()==$tagModel->getApprovedStatus()) {
                Mage::register('tag_model', $tagModel);
                $this->loadLayout(false);
                $this->renderLayout();
                return;
            }
        }
        $this->_forward('nofeed', 'index', 'rss');
    }

    public function notifystockAction()
    {
        Mage::helper('rss')->authAdmin('catalog/products');
        $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
        $this->loadLayout(false);
        $this->renderLayout();
    }

    public function reviewAction()
    {
        Mage::helper('rss')->authAdmin('catalog/reviews_ratings');
        $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
        $this->loadLayout(false);
        $this->renderLayout();
    }

    public function categoryAction()
    {
        if ($this->checkFeedEnable('category')) {
            $this->loadLayout(false);
            $this->renderLayout();
        }
    }
}