<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Sales order shippment API
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Order_Shipment_Api extends Mage_Sales_Model_Api_Resource
{
    public function __construct()
    {
        $this->_attributesMap['shipment'] = ['shipment_id' => 'entity_id'];
        $this->_attributesMap['shipment_item'] = ['item_id' => 'entity_id'];
        $this->_attributesMap['shipment_comment'] = ['comment_id' => 'entity_id'];
        $this->_attributesMap['shipment_track'] = ['track_id' => 'entity_id'];
    }

    /**
     * Retrieve shipments by filters
     *
     * @param null|array|object $filters
     * @return array
     */
    public function items($filters = null)
    {
        $shipments = [];
        //TODO: add full name logic
        $shipmentCollection = Mage::getResourceModel('sales/order_shipment_collection')
            ->addAttributeToSelect('increment_id')
            ->addAttributeToSelect('created_at')
            ->addAttributeToSelect('total_qty')
            ->joinAttribute('shipping_firstname', 'order_address/firstname', 'shipping_address_id', null, 'left')
            ->joinAttribute('shipping_lastname', 'order_address/lastname', 'shipping_address_id', null, 'left')
            ->joinAttribute('order_increment_id', 'order/increment_id', 'order_id', null, 'left')
            ->joinAttribute('order_created_at', 'order/created_at', 'order_id', null, 'left');

        $apiHelper = Mage::helper('api');
        try {
            $filters = $apiHelper->parseFilters($filters, $this->_attributesMap['shipment']);
            foreach ($filters as $field => $value) {
                $shipmentCollection->addFieldToFilter($field, $value);
            }
        } catch (Mage_Core_Exception $mageCoreException) {
            $this->_fault('filters_invalid', $mageCoreException->getMessage());
        }

        foreach ($shipmentCollection as $shipment) {
            $shipments[] = $this->_getAttributes($shipment, 'shipment');
        }

        return $shipments;
    }

    /**
     * Retrieve shipment information
     *
     * @param string $shipmentIncrementId
     * @return array
     * @throws Mage_Api_Exception
     */
    public function info($shipmentIncrementId)
    {
        $shipment = Mage::getModel('sales/order_shipment')->loadByIncrementId($shipmentIncrementId);

        if (!$shipment->getId()) {
            $this->_fault('not_exists');
        }

        $result = $this->_getAttributes($shipment, 'shipment');

        $result['items'] = [];
        foreach ($shipment->getAllItems() as $item) {
            $result['items'][] = $this->_getAttributes($item, 'shipment_item');
        }

        $result['tracks'] = [];
        foreach ($shipment->getAllTracks() as $track) {
            $result['tracks'][] = $this->_getAttributes($track, 'shipment_track');
        }

        $result['comments'] = [];
        foreach ($shipment->getCommentsCollection() as $comment) {
            $result['comments'][] = $this->_getAttributes($comment, 'shipment_comment');
        }

        return $result;
    }

    /**
     * Create new shipment for order
     *
     * @param string $orderIncrementId
     * @param array $itemsQty
     * @param string $comment
     * @param bool $email
     * @param bool $includeComment
     * @return null|string
     */
    public function create(
        $orderIncrementId,
        $itemsQty = [],
        $comment = null,
        $email = false,
        $includeComment = false
    ) {
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);

        /**
         * Check order existing
         */
        if (!$order->getId()) {
            $this->_fault('order_not_exists');
        }

        /**
         * Check shipment create availability
         */
        if (!$order->canShip()) {
            $this->_fault('data_invalid', Mage::helper('sales')->__('Cannot do shipment for order.'));
        }

        $shipment = $order->prepareShipment($itemsQty);
        if ($shipment) {
            $shipment->register();
            $shipment->addComment($comment, $email && $includeComment);
            if ($email) {
                $shipment->setEmailSent(true);
            }

            $shipment->getOrder()->setIsInProcess(true);
            try {
                Mage::getModel('core/resource_transaction')
                    ->addObject($shipment)
                    ->addObject($shipment->getOrder())
                    ->save();
                $shipment->sendEmail($email, ($includeComment ? $comment : ''));
            } catch (Mage_Core_Exception $e) {
                $this->_fault('data_invalid', $e->getMessage());
            }

            return $shipment->getIncrementId();
        }

        return null;
    }

    /**
     * Add tracking number to order
     *
     * @param string $shipmentIncrementId
     * @param string $carrier
     * @param string $title
     * @param string $trackNumber
     * @param null|float $weight
     * @return int
     */
    public function addTrack($shipmentIncrementId, $carrier, $title, $trackNumber, $weight = null)
    {
        $shipment = Mage::getModel('sales/order_shipment')->loadByIncrementId($shipmentIncrementId);

        if (!$shipment->getId()) {
            $this->_fault('not_exists');
        }

        $carriers = $this->_getCarriers($shipment);

        if (!isset($carriers[$carrier])) {
            $this->_fault('data_invalid', Mage::helper('sales')->__('Invalid carrier specified.'));
        }

        $track = Mage::getModel('sales/order_shipment_track')
                    ->setNumber($trackNumber)
                    ->setCarrierCode($carrier)
                    ->setTitle($title);

        if (!empty($weight)) {
            $track->setWeight($weight);
        }

        $shipment->addTrack($track);

        try {
            $shipment->save();
            $track->save();
        } catch (Mage_Core_Exception $mageCoreException) {
            $this->_fault('data_invalid', $mageCoreException->getMessage());
        }

        return $track->getId();
    }

    /**
     * Remove tracking number
     *
     * @param string $shipmentIncrementId
     * @param int $trackId
     * @return bool
     */
    public function removeTrack($shipmentIncrementId, $trackId)
    {
        $shipment = Mage::getModel('sales/order_shipment')->loadByIncrementId($shipmentIncrementId);

        if (!$shipment->getId()) {
            $this->_fault('not_exists');
        }

        if (!$track = $shipment->getTrackById($trackId)) {
            $this->_fault('track_not_exists');
        }

        try {
            $track->delete();
        } catch (Mage_Core_Exception $mageCoreException) {
            $this->_fault('track_not_deleted', $mageCoreException->getMessage());
        }

        return true;
    }

    /**
     * Send email with shipment data to customer
     *
     * @param string $shipmentIncrementId
     * @param string $comment
     * @return bool
     */
    public function sendInfo($shipmentIncrementId, $comment = '')
    {
        $shipment = Mage::getModel('sales/order_shipment')->loadByIncrementId($shipmentIncrementId);

        if (!$shipment->getId()) {
            $this->_fault('not_exists');
        }

        try {
            $shipment->sendEmail(true, $comment)
                ->setEmailSent(true)
                ->save();
            $historyItem = Mage::getResourceModel('sales/order_status_history_collection')
                ->getUnnotifiedForInstance($shipment, Mage_Sales_Model_Order_Shipment::HISTORY_ENTITY_NAME);
            if ($historyItem) {
                $historyItem->setIsCustomerNotified(1);
                $historyItem->save();
            }
        } catch (Mage_Core_Exception $mageCoreException) {
            $this->_fault('data_invalid', $mageCoreException->getMessage());
        }

        return true;
    }

    /**
     * Retrieve tracking number info
     *
     * @param string $shipmentIncrementId
     * @param int $trackId
     * @return mixed
     */
    public function infoTrack($shipmentIncrementId, $trackId)
    {
        $shipment = Mage::getModel('sales/order_shipment')->loadByIncrementId($shipmentIncrementId);

        if (!$shipment->getId()) {
            $this->_fault('not_exists');
        }

        if (!$track = $shipment->getTrackById($trackId)) {
            $this->_fault('track_not_exists');
        }

        $info = $track->getNumberDetail();

        if (is_object($info)) {
            $info = $info->toArray();
        }

        return $info;
    }

    /**
     * Add comment to shipment
     *
     * @param string $shipmentIncrementId
     * @param string $comment
     * @param bool $email
     * @param bool $includeInEmail
     * @return bool
     */
    public function addComment($shipmentIncrementId, $comment, $email = false, $includeInEmail = false)
    {
        $shipment = Mage::getModel('sales/order_shipment')->loadByIncrementId($shipmentIncrementId);

        if (!$shipment->getId()) {
            $this->_fault('not_exists');
        }

        try {
            $shipment->addComment($comment, $email);
            $shipment->sendUpdateEmail($email, ($includeInEmail ? $comment : ''));
            $shipment->save();
        } catch (Mage_Core_Exception $mageCoreException) {
            $this->_fault('data_invalid', $mageCoreException->getMessage());
        }

        return true;
    }

    /**
     * Retrieve allowed shipping carriers for specified order
     *
     * @param string $orderIncrementId
     * @return array
     */
    public function getCarriers($orderIncrementId)
    {
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);

        /**
         * Check order existing
         */
        if (!$order->getId()) {
            $this->_fault('order_not_exists');
        }

        return $this->_getCarriers($order);
    }

    /**
     * Retrieve shipping carriers for specified order
     *
     * @param Mage_Eav_Model_Entity_Abstract $object
     * @return array
     */
    protected function _getCarriers($object)
    {
        $carriers = [];
        $carrierInstances = Mage::getSingleton('shipping/config')->getAllCarriers(
            $object->getStoreId(),
        );

        $carriers['custom'] = Mage::helper('sales')->__('Custom Value');
        foreach ($carrierInstances as $code => $carrier) {
            if ($carrier->isTrackingAvailable()) {
                $carriers[$code] = $carrier->getConfigData('title');
            }
        }

        return $carriers;
    }
}
