<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml summary rating stars
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Review_Rating_Summary extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        $this->setTemplate('rating/stars/summary.phtml');
        $this->setReviewId(Mage::registry('review_data')->getId());
    }

    public function getRating()
    {
        if (!$this->getRatingCollection()) {
            $ratingCollection = Mage::getModel('rating/rating_option_vote')
                ->getResourceCollection()
                ->setReviewFilter($this->getReviewId())
                ->addRatingInfo()
                ->load();
            $this->setRatingCollection(($ratingCollection->getSize()) ? $ratingCollection : false);
        }
        return $this->getRatingCollection();
    }

    public function getRatingSummary()
    {
        if (!$this->getRatingSummaryCache()) {
            $this->setRatingSummaryCache(Mage::getModel('rating/rating')->getReviewSummary($this->getReviewId()));
        }

        return $this->getRatingSummaryCache();
    }
}
