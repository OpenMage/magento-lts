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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * XmlConnect catalog controller
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_CatalogController extends Mage_XmlConnect_Controller_Action
{
    /**
     * Category list
     *
     * @return void
     */
    public function categoryAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * Filter product list
     *
     * @return void
     */
    public function filtersAction()
    {
        try{
            $this->loadLayout(false);
            $this->renderLayout();
        } catch (Mage_Core_Exception $e) {
            $this->_message($e->getMessage(), self::MESSAGE_STATUS_ERROR);
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_message(
                $this->__('An error occurred while loading category filters.'),
                self::MESSAGE_STATUS_ERROR
            );
        }
    }

    /**
     * Product information
     *
     * @return void
     */
    public function productAction()
    {
        try {
            $this->loadLayout(false);
            $this->renderLayout();
            return;
        } catch (Mage_Core_Exception $e) {
            $this->_message($e->getMessage(), self::MESSAGE_STATUS_ERROR);
        } catch (Exception $e) {
            $this->_message($this->__('Unable to load product info.'), self::MESSAGE_STATUS_ERROR);
            Mage::logException($e);
        }
    }

    /**
     * Product options list
     *
     * @return void
     */
    public function productOptionsAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * Product gallery images list
     *
     * @return void
     */
    public function productGalleryAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * Product reviews list
     *
     * @return void
     */
    public function productReviewsAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * Add new review
     *
     * @return void
     */
    public function productReviewAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * Perform search products
     *
     * @return void
     */
    public function searchAction()
    {
        /** @var $_helper Mage_CatalogSearch_Helper_Data */
        $_helper = Mage::helper('catalogsearch');
        $queryParam = str_replace('%20', ' ', $this->getRequest()->getParam('query'));
        $this->getRequest()->setParam($_helper->getQueryParamName(), $queryParam);
        /** @var $query Mage_CatalogSearch_Model_Query */
        $query = $_helper->getQuery();
        $query->setStoreId(Mage::app()->getStore()->getId());

        if ($query->getQueryText()) {
            if ($_helper->isMinQueryLength()) {
                $query->setId(0)
                    ->setIsActive(1)
                    ->setIsProcessed(1);
            } else {
                if ($query->getId()) {
                    $query->setPopularity($query->getPopularity()+1);
                } else {
                    $query->setPopularity(1);
                }

                /**
                 * We don't support redirect at this moment
                 *
                 * @todo add redirect support for mobile application
                 */
                if (false && $query->getRedirect()) {
                    $query->save();
                    $this->getResponse()->setRedirect($query->getRedirect());
                    return;
                } else {
                    $query->prepare();
                }
            }

            $_helper->checkNotes();

            if (!$_helper->isMinQueryLength()) {
                $query->save();
            }
        }

        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * Retrieve suggestions based on search query
     *
     * @return void
     */
    public function searchSuggestAction()
    {
        $this->getRequest()->setParam('q', $this->getRequest()->getParam('query'));
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * Send product link to friend action
     *
     * @return this
     */
    public function sendEmailAction()
    {
        /* @var $helper Mage_Sendfriend_Helper_Data */
        $helper = Mage::helper('sendfriend');
        /* @var $session Mage_Customer_Model_Session */
        $session = Mage::getSingleton('customer/session');

        if (!$helper->isEnabled()) {
            $this->_message($this->__('Tell a Friend is disabled.'), self::MESSAGE_STATUS_ERROR);
            return $this;
        }

        if (!$helper->isAllowForGuest() && !$session->isLoggedIn()) {
            $this->_message($this->__('Customer not logged in.'), self::MESSAGE_STATUS_ERROR);
            return $this;
        }

        /**
         * Initialize product
         */
        $productId  = (int)$this->getRequest()->getParam('product_id');
        if (!$productId) {
            $this->_message($this->__('No product selected.'), self::MESSAGE_STATUS_ERROR);
            return $this;
        }
        $product = Mage::getModel('catalog/product')
            ->load($productId);
        if (!$product->getId() || !$product->isVisibleInCatalog()) {
            $this->_message($this->__('Selected product is unavailable.'), self::MESSAGE_STATUS_ERROR);
            return $this;
        }

        Mage::register('product', $product);

        /**
         * Initialize send friend model
         */
        $model  = Mage::getModel('sendfriend/sendfriend');
        $model->setRemoteAddr(Mage::helper('core/http')->getRemoteAddr(true));
        $model->setCookie(Mage::app()->getCookie());
        $model->setWebsiteId(Mage::app()->getStore()->getWebsiteId());

        Mage::register('send_to_friend_model', $model);
/*
        if ($model->getMaxSendsToFriend()) {
            $this->_message($this->__('Messages cannot be sent more than %d times in an hour.',
                    $model->getMaxSendsToFriend()),
                    self::MESSAGE_STATUS_WARNING);
            return $this;
        }
*/
        $data = $this->getRequest()->getPost();

        if (!$data) {
            $this->_message($this->__('Specified invalid data.'), self::MESSAGE_STATUS_ERROR);
            return $this;
        }

        $sender = (array)$this->getRequest()->getPost('sender');
        if ($session->isLoggedIn()) {
            $sender['email'] = $session->getCustomer()->getEmail();
            $sender['name'] = $session->getCustomer()->getFirstName() . ' ' . $session->getCustomer()->getLastName();
        }

        /**
         * Initialize category and set it to product
         */
        $categoryId = $this->getRequest()->getParam('category_id', null);
        if ($categoryId) {
            $category = Mage::getModel('catalog/category')
                ->load($categoryId);
            $product->setCategory($category);
            Mage::register('current_category', $category);
        }

        $model->setSender($sender);
        $model->setRecipients($this->getRequest()->getPost('recipients'));
        $model->setProduct($product);

        try {
            $validate = $model->validate();
            if ($validate === true) {
                $model->send();
                $this->_message($this->__('Tell a Friend link has been sent.'), self::MESSAGE_STATUS_SUCCESS);
                return;
            } else {
                if (is_array($validate)) {
                    $this->_message(implode(' ', $validate), self::MESSAGE_STATUS_ERROR);
                    return;
                } else {
                    $this->_message($this->__('There were some problems with the data.'), self::MESSAGE_STATUS_ERROR);
                    return;
                }
            }
        } catch (Mage_Core_Exception $e) {
            $this->_message($e->getMessage(), self::MESSAGE_STATUS_ERROR);
        } catch (Exception $e) {
            $this->_message($this->__('Some emails were not sent.'), self::MESSAGE_STATUS_ERROR);
        }
        return $this;
    }
}
