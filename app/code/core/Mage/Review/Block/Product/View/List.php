<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Review
 */

/**
 * Detailed Product Reviews
 *
 * @package    Mage_Review
 */
class Mage_Review_Block_Product_View_List extends Mage_Review_Block_Product_View
{
    protected $_forceHasOptions = false;

    /**
     * @return int
     */
    public function getProductId()
    {
        return Mage::registry('product')->getId();
    }

    /**
     * @return $this
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        if ($toolbar = $this->getLayout()->getBlock('product_review_list.toolbar')) {
            $toolbar->setCollection($this->getReviewsCollection());
            $this->setChild('toolbar', $toolbar);
        }

        return $this;
    }

    /**
     * @inheritDoc
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function _beforeToHtml()
    {
        $this->getReviewsCollection()
            ->load()
            ->addRateVotes();
        return parent::_beforeToHtml();
    }

    /**
     * @param int $id
     * @return string
     */
    public function getReviewUrl($id)
    {
        return Mage::getUrl('review/product/view', ['id' => $id]);
    }
}
