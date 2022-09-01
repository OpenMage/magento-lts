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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml Rss Controller
 *
 * @category   Mage
 * @package    Mage_Rss
 * @author     Magento Core Team <core@magentocommerce.com>
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
