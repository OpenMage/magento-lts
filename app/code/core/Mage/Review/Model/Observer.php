<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Review
 */

/**
 * Review Observer Model
 *
 * @package    Mage_Review
 */
class Mage_Review_Model_Observer
{
    /**
     * Add review summary info for tagged product collection
     *
     * @return $this
     */
    public function tagProductCollectionLoadAfter(Varien_Event_Observer $observer)
    {
        /** @var Mage_Tag_Model_Resource_Product_Collection $collection */
        $collection = $observer->getEvent()->getCollection();
        Mage::getSingleton('review/review')
            ->appendSummary($collection);

        return $this;
    }

    /**
     * Cleanup product reviews after product delete
     *
     * @return $this
     */
    public function processProductAfterDeleteEvent(Varien_Event_Observer $observer)
    {
        /** @var Mage_Catalog_Model_Product $eventProduct */
        $eventProduct = $observer->getEvent()->getProduct();
        if ($eventProduct && $eventProduct->getId()) {
            Mage::getResourceSingleton('review/review')->deleteReviewsByProductId($eventProduct->getId());
        }

        return $this;
    }

    /**
     * Append review summary before rendering html
     *
     * @return $this
     */
    public function catalogBlockProductCollectionBeforeToHtml(Varien_Event_Observer $observer)
    {
        /** @var Mage_Catalog_Model_Resource_Product_Collection $productCollection */
        $productCollection = $observer->getEvent()->getCollection();
        if ($productCollection instanceof Varien_Data_Collection) {
            $productCollection->load();
            Mage::getModel('review/review')->appendSummary($productCollection);
        }

        return $this;
    }
}
