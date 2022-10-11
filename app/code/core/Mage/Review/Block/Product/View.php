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
 * @category    Mage
 * @package     Mage_Review
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product Reviews Page
 *
 * @category   Mage
 * @package    Mage_Review
 * @author     Magento Core Team <core@magentocommerce.com>
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
     * @param Mage_Catalog_Model_Product $product
     * @param bool $templateType
     * @param bool $displayIfNoReviews
     * @return string
     * @throws Mage_Core_Model_Store_Exception|Mage_Core_Exception
     */
    public function getReviewsSummaryHtml(Mage_Catalog_Model_Product $product, $templateType = false, $displayIfNoReviews = false)
    {
        /** @var Mage_Core_Block_Template $reviewContBlock */
        $reviewContBlock = $this->getLayout()->getBlock('product_review_list.count');
        return
            $this->getLayout()->createBlock('rating/entity_detailed')
                ->setEntityId($this->getProduct()->getId())
                ->toHtml()
            .
            $reviewContBlock
                ->assign('count', $this->getReviewsCollection()->getSize())
                ->toHtml()
            ;
    }

    /**
     * @return Mage_Review_Model_Resource_Review_Collection
     * @throws Mage_Core_Model_Store_Exception|Mage_Core_Exception
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
