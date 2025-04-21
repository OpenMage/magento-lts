<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml detailed rating stars
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Review_Rating_Detailed extends Mage_Adminhtml_Block_Template
{
    protected $_voteCollection = false;
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('rating/detailed.phtml');
        if (Mage::registry('review_data')) {
            $this->setReviewId(Mage::registry('review_data')->getReviewId());
        }
    }

    public function getRating()
    {
        if (!$this->getRatingCollection()) {
            if (Mage::registry('review_data')) {
                $stores = Mage::registry('review_data')->getStores();

                $stores = array_diff($stores, [0]);

                $ratingCollection = Mage::getModel('rating/rating')
                    ->getResourceCollection()
                    ->addEntityFilter('product')
                    ->setStoreFilter($stores)
                    ->setPositionOrder()
                    ->load()
                    ->addOptionToItems();

                $this->_voteCollection = Mage::getModel('rating/rating_option_vote')
                    ->getResourceCollection()
                    ->setReviewFilter($this->getReviewId())
                    ->addOptionInfo()
                    ->load()
                    ->addRatingOptions();
            } elseif (!$this->getIsIndependentMode()) {
                $ratingCollection = Mage::getModel('rating/rating')
                    ->getResourceCollection()
                    ->addEntityFilter('product')
                    ->setStoreFilter(Mage::app()->getDefaultStoreView()->getId())
                    ->setPositionOrder()
                    ->load()
                    ->addOptionToItems();
            } else {
                $ratingCollection = Mage::getModel('rating/rating')
                    ->getResourceCollection()
                    ->addEntityFilter('product')
                    ->setStoreFilter(
                        $this->getRequest()->getParam('select_stores')
                            ? $this->getRequest()->getParam('select_stores')
                            : $this->getRequest()->getParam('stores'),
                    )
                    ->setPositionOrder()
                    ->load()
                    ->addOptionToItems();
                if ((int) $this->getRequest()->getParam('id')) {
                    $this->_voteCollection = Mage::getModel('rating/rating_option_vote')
                        ->getResourceCollection()
                        ->setReviewFilter((int) $this->getRequest()->getParam('id'))
                        ->addOptionInfo()
                        ->load()
                        ->addRatingOptions();
                }
            }
            $this->setRatingCollection(($ratingCollection->getSize()) ? $ratingCollection : false);
        }
        return $this->getRatingCollection();
    }

    public function setIndependentMode()
    {
        $this->setIsIndependentMode(true);
        return $this;
    }

    public function isSelected($option, $rating)
    {
        if ($this->getIsIndependentMode()) {
            $ratings = $this->getRequest()->getParam('ratings');

            if (isset($ratings[$option->getRatingId()])) {
                return $option->getId() == $ratings[$option->getRatingId()];
            } elseif (!$this->_voteCollection) {
                return false;
            }
        }

        if ($this->_voteCollection) {
            foreach ($this->_voteCollection as $vote) {
                if ($option->getId() == $vote->getOptionId()) {
                    return true;
                }
            }
        }

        return false;
    }
}
