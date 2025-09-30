<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Shipment tracking control form
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_Shipment_Create_Tracking extends Mage_Adminhtml_Block_Template
{
    /**
     * Prepares layout of block
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->setChild(
            'add_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label'   => Mage::helper('sales')->__('Add Tracking Number'),
                    'class'   => '',
                    'onclick' => 'trackingControl.add()',
                ]),
        );
        return $this;
    }

    /**
     * Retrieve shipment model instance
     *
     * @return Mage_Sales_Model_Order_Shipment
     */
    public function getShipment()
    {
        return Mage::registry('current_shipment');
    }

    /**
     * Retrieve
     *
     * @return array
     */
    public function getCarriers()
    {
        $carriers = [];
        $carrierInstances = Mage::getSingleton('shipping/config')->getAllCarriers(
            $this->getShipment()->getStoreId(),
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
