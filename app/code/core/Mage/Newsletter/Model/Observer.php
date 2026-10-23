<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Newsletter
 */

/**
 * Newsletter module observer
 *
 * @package    Mage_Newsletter
 */
class Mage_Newsletter_Model_Observer
{
    /**
     * @return $this
     */
    public function subscribeCustomer(Varien_Event_Observer $observer)
    {
        $customer = $observer->getEvent()->getCustomer();
        if (($customer instanceof Mage_Customer_Model_Customer)) {
            Mage::getModel('newsletter/subscriber')->subscribeCustomer($customer);
        }

        return $this;
    }

    /**
     * Customer delete handler
     *
     * @return $this
     */
    public function customerDeleted(Varien_Event_Observer $observer)
    {
        $subscriber = Mage::getModel('newsletter/subscriber')
            ->loadByEmail($observer->getEvent()->getCustomer()->getEmail());
        if ($subscriber->getId()) {
            $subscriber->delete();
        }

        return $this;
    }

    /**
     * @param Varien_Event_Observer $schedule
     */
    public function scheduledSend($schedule)
    {
        $countOfQueue  = 3;
        $countOfSubscritions = 20;

        /** @var Mage_Newsletter_Model_Resource_Queue_Collection $collection */
        $collection = Mage::getModel('newsletter/queue')->getCollection()
            ->setPageSize($countOfQueue)
            ->setCurPage(1)
            ->addOnlyForSendingFilter()
            ->load();

        $collection->walk('sendPerSubscriber', [$countOfSubscritions]);
    }
}
