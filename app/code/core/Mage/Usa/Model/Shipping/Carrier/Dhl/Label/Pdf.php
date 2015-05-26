<?php
/**
 * Magento
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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Usa
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * DHL International (API v1.4) Label Creation
 *
 * @deprecated now the process of creating the label is on DHL side
 * @category Mage
 * @package  Mage_Usa
 * @author   Magento Core Team <core@magentocommerce.com>
 */
class Mage_Usa_Model_Shipping_Carrier_Dhl_Label_Pdf
{
    /**
     * Label Information
     *
     * @var SimpleXMLElement
     */
    protected $_info;

    /**
     * Shipment Request
     *
     * @var Mage_Shipping_Model_Shipment_Request
     */
    protected $_request;

    /**
     * Dhl International Label Creation Class constructor
     *
     * @param array $arguments
     */
    public function __construct(array $arguments)
    {
        $this->_info    = $arguments['info'];
        $this->_request = $arguments['request'];
    }

    /**
     * Create Label
     *
     * @return string
     */
    public function render()
    {
        $pdf = new Zend_Pdf();

        $pdfBuilder = new Mage_Usa_Model_Shipping_Carrier_Dhl_Label_Pdf_PageBuilder();

        $template = new Mage_Usa_Model_Shipping_Carrier_Dhl_Label_Pdf_Page(Zend_Pdf_Page::SIZE_A4_LANDSCAPE);
        $pdfBuilder->setPage($template)
            ->addProductName((string)$this->_info->ProductShortName)
            ->addProductContentCode((string)$this->_info->ProductContentCode)
        //->addUnitId({unitId})
        //->addReferenceData({referenceData})
            ->addSenderInfo($this->_info->Shipper)
            ->addOriginInfo((string)$this->_info->OriginServiceArea->ServiceAreaCode)
            ->addReceiveInfo($this->_info->Consignee)
            ->addDestinationFacilityCode(
                (string)$this->_info->Consignee->CountryCode,
                (string)$this->_info->DestinationServiceArea->ServiceAreaCode,
                (string)$this->_info->DestinationServiceArea->FacilityCode
            )
            ->addServiceFeaturesCodes()
            ->addDeliveryDateCode()
            ->addShipmentInformation($this->_request->getOrderShipment())
            ->addDateInfo($this->_info->ShipmentDate)
            ->addWeightInfo((string)$this->_info->ChargeableWeight, (string)$this->_info->WeightUnit)
            ->addWaybillBarcode((string)$this->_info->AirwayBillNumber, (string)$this->_info->Barcodes->AWBBarCode)
            ->addRoutingBarcode(
                (string)$this->_info->DHLRoutingCode,
                (string)$this->_info->DHLRoutingDataId,
                (string)$this->_info->Barcodes->DHLRoutingBarCode
            )
            ->addBorder();

        $packages = array_values($this->_request->getPackages());
        $i = 0;
        foreach ($this->_info->Pieces->Piece as $piece) {
            $page = new Mage_Usa_Model_Shipping_Carrier_Dhl_Label_Pdf_Page($template);
            $pdfBuilder->setPage($page)
                ->addPieceNumber((int)$piece->PieceNumber, (int)$this->_info->Piece)
                ->addContentInfo($packages[$i])
                ->addPieceIdBarcode(
                    (string)$piece->DataIdentifier,
                    (string)$piece->LicensePlate,
                    (string)$piece->LicensePlateBarCode
                );
            array_push($pdf->pages, $page);
            $i++;
        }
        return $pdf->render();
    }
}
