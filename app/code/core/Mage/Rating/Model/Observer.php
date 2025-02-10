<?php
/**
 * Rating Observer Model
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Rating
 */
class Mage_Rating_Model_Observer
{
    /**
     * Cleanup product ratings after product delete
     *
     * @return Mage_Rating_Model_Observer
     */
    public function processProductAfterDeleteEvent(Varien_Event_Observer $observer)
    {
        /** @var Mage_Catalog_Model_Product $eventProduct */
        $eventProduct = $observer->getEvent()->getProduct();
        if ($eventProduct && $eventProduct->getId()) {
            Mage::getResourceSingleton('rating/rating')->deleteAggregatedRatingsByProductId($eventProduct->getId());
        }
        return $this;
    }
}
