<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Rating
 */

/**
 * Entity rating block
 *
 * @package    Mage_Rating
 *
 * @method $this setEntityId(int $value)
 */
class Mage_Rating_Block_Entity_Detailed extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('rating/detailed.phtml');
    }

    /**
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function _toHtml()
    {
        $entityId = Mage::app()->getRequest()->getParam('id');
        if ((int) $entityId <= 0) {
            return '';
        }

        $reviewsCount = Mage::getModel('review/review')
            ->getTotalReviews($entityId, true);
        if ($reviewsCount == 0) {
            #return Mage::helper('rating')->__('Be the first to review this product');
            $this->setTemplate('rating/empty.phtml');
            return parent::_toHtml();
        }

        $ratingCollection = Mage::getModel('rating/rating')
            ->getResourceCollection()
            ->addEntityFilter('product') # @todo TOFIX
            ->setPositionOrder()
            ->setStoreFilter(Mage::app()->getStore()->getId())
            ->addRatingPerStoreName(Mage::app()->getStore()->getId())
            ->load();

        if ($entityId) {
            $ratingCollection->addEntitySummaryToItem($entityId, Mage::app()->getStore()->getId());
        }

        $this->assign('collection', $ratingCollection);
        return parent::_toHtml();
    }
}
