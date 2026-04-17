<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Review
 */

/**
 * Product Reviews Page
 *
 * @package    Mage_Review
 */
class Mage_Review_Block_Product_View extends Mage_Catalog_Block_Product_View
{
    protected $_reviewsCollection;

    /**
     * Render block HTML
     *
     * @inheritDoc
     * @throws Mage_Core_Exception
     */
    protected function _toHtml()
    {
        $this->getProduct()->setShortDescription(null);

        return parent::_toHtml();
    }

    /**
     * Replace review summary html with more detailed review summary
     * Reviews collection count will be jerked here
     *
     * @param  false|string                                        $templateType
     * @param  bool                                                $displayIfNoReviews
     * @return string
     * @throws Mage_Core_Exception|Mage_Core_Model_Store_Exception
     */
    public function getReviewsSummaryHtml(Mage_Catalog_Model_Product $product, $templateType = false, $displayIfNoReviews = false)
    {
        /** @var Mage_Core_Block_Template $reviewContBlock */
        $reviewContBlock = $this->getLayout()->getBlock('product_review_list.count');
        return
            $this->getLayout()->createBlock('rating/entity_detailed')
                ->setEntityId($this->getProduct()->getId())
                ->toHtml()
            . $reviewContBlock
                ->assign('count', $this->getReviewsCollection()->getSize())
                ->toHtml()
        ;
    }

    /**
     * @return Mage_Review_Model_Resource_Review_Collection
     * @throws Mage_Core_Exception|Mage_Core_Model_Store_Exception
     */
    public function getReviewsCollection()
    {
        if ($this->_reviewsCollection === null) {
            $this->_reviewsCollection = Mage::getModel('review/review')->getCollection()
                ->addStoreFilter(Mage::app()->getStore()->getId())
                ->addStatusFilter(Mage_Review_Model_Review::STATUS_APPROVED)
                ->addEntityFilter('product', $this->getProduct()->getId())
                ->setDateOrder();
        }

        return $this->_reviewsCollection;
    }

    /**
     * Force product view page behave like without options
     *
     * @return false
     */
    public function hasOptions()
    {
        return false;
    }
}
