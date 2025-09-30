<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Review
 */

/**
 * Customer reviews controller
 *
 * @package    Mage_Review
 */
class Mage_Review_CustomerController extends Mage_Core_Controller_Front_Action
{
    /**
     * Action predispatch
     *
     * Check customer authentication for some actions
     */
    public function preDispatch()
    {
        parent::preDispatch();
        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
        return $this;
    }

    /**
     * Load review model with data by passed id.
     * Return false if review was not loaded or was not created by customer
     *
     * @param int $reviewId
     * @return bool|Mage_Review_Model_Review
     */
    protected function _loadReview($reviewId)
    {
        if (!$reviewId) {
            return false;
        }

        /** @var Mage_Review_Model_Review $review */
        $review = Mage::getModel('review/review')->load($reviewId);
        if (!$review->getId() || $review->getCustomerId() != Mage::getSingleton('customer/session')->getCustomerId()) {
            return false;
        }

        return $review;
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');

        if ($navigationBlock = $this->getLayout()->getBlock('customer_account_navigation')) {
            $navigationBlock->setActive('review/customer');
        }
        if ($block = $this->getLayout()->getBlock('review_customer_list')) {
            $block->setRefererUrl($this->_getRefererUrl());
        }

        $this->getLayout()->getBlock('head')->setTitle($this->__('My Product Reviews'));

        $this->renderLayout();
    }

    public function viewAction()
    {
        $review = $this->_loadReview((int) $this->getRequest()->getParam('id'));
        if (!$review) {
            $this->_redirect('*/*');
            return;
        }

        $this->loadLayout();
        if ($navigationBlock = $this->getLayout()->getBlock('customer_account_navigation')) {
            $navigationBlock->setActive('review/customer');
        }
        $this->getLayout()->getBlock('head')->setTitle($this->__('Review Details'));
        $this->renderLayout();
    }
}
