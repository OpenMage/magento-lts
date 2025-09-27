<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Usa
 */

/**
 * DHL International (API v1.4) Label Creation
 *
 * @package    Mage_Usa
 * @deprecated now the process of creating the label is on DHL side
 *
 * @property Zend_Pdf_Resource_Font $_fontBold
 * @property Zend_Pdf_Resource_Font $_fontNormal
 */
class Mage_Usa_Model_Shipping_Carrier_Dhl_Label_Pdf_PageBuilder
{
    /**
     * X coordinate of a block
     */
    public const X_INDENT = 60;

    /**
     * Y coordinate of a block
     */
    public const Y_INDENT = 15;

    /**
     * Pdf Page Instance
     *
     * @var Zend_Pdf_Page
     */
    protected $_page;

    /**
     * Create font instances
     */
    public function __construct()
    {
        $this->_fontNormal = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
        $this->_fontBold = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD);
    }

    /**
     * Get Page
     *
     * @return Zend_Pdf_Page
     */
    public function getPage()
    {
        return $this->_page;
    }

    /**
     * Set Page
     *
     * @return $this
     */
    public function setPage(Zend_Pdf_Page $page)
    {
        $this->_page = $page;
        return $this;
    }

    /**
     * Calculate x coordinate with indentation
     *
     * @param int|float $pt
     * @return int
     * @SuppressWarnings("PHPMD.ShortMethodName")
     */
    protected function _x($pt)
    {
        return $pt + self::X_INDENT;
    }

    /**
     * Calculate y coordinate with indentation
     *
     * @param int|float $pt
     * @return int
     * @SuppressWarnings("PHPMD.ShortMethodName")
     */
    protected function _y($pt)
    {
        return 595 - self::Y_INDENT - $pt;
    }

    /**
     * Add Border
     *
     * @return $this
     */
    public function addBorder()
    {
        $x = $this->_x(0);
        $y = $this->_y(0);

        $image = new Zend_Pdf_Resource_Image_Jpeg(Mage::getBaseDir('media') . DS . 'dhl' . DS . 'logo.jpg');
        $this->_page->drawImage($image, $x + 191, $this->_y(27), $x + 287, $this->_y(1));

        /* Vertical borders */
        $this->_page->drawLine($x, $y, $x, $this->_y(568));
        $this->_page->drawLine($x + 287.5, $y, $x + 287.5, $this->_y(568));
        $this->_page->drawLine($x + 139.5, $y, $x + 139.5, $this->_y(28));
        $this->_page->drawLine($x + 190.5, $y, $x + 190.5, $this->_y(28));

        /* Horisontal borders */
        $this->_page->drawLine($x, $y, $x + 288, $y);
        $this->_page->drawLine($x, $this->_y(28), $x + 288, $this->_y(28));
        $this->_page->drawLine($x, $this->_y(80.5), $x + 288, $this->_y(80.5));
        $this->_page->drawLine($x, $this->_y(164), $x + 288, $this->_y(164));
        $this->_page->drawLine($x, $this->_y(194), $x + 288, $this->_y(194));
        $this->_page->drawLine($x, $this->_y(217.5), $x + 288, $this->_y(217.5));
        $this->_page->drawLine($x, $this->_y(245.5), $x + 288, $this->_y(245.5));
        $this->_page->drawLine($x, $this->_y(568.5), $x + 288, $this->_y(568.5));

        $this->_page->setLineWidth(0.3);
        $x = $this->_x(3);
        $y = $this->_y(83);
        $this->_page->drawLine($x, $y, $x + 10, $y);
        $this->_page->drawLine($x, $y, $x, $y - 10);

        $x = $this->_x(3);
        $y = $this->_y(161);
        $this->_page->drawLine($x, $y, $x + 10, $y);
        $this->_page->drawLine($x, $y, $x, $y + 10);

        $x = $this->_x(285);
        $y = $this->_y(83);
        $this->_page->drawLine($x, $y, $x - 10, $y);
        $this->_page->drawLine($x, $y, $x, $y - 10);

        $x = $this->_x(285);
        $y = $this->_y(161);
        $this->_page->drawLine($x, $y, $x - 10, $y);
        $this->_page->drawLine($x, $y, $x, $y + 10);

        return $this;
    }

    /**
     * Add Product Name
     *
     * @param string $name
     * @return $this
     * @throws InvalidArgumentException
     */
    public function addProductName($name)
    {
        $this->_page->saveGS();
        $this->_page->setFont($this->_fontBold, 9);
        if (!strlen($name)) {
            throw new InvalidArgumentException(Mage::helper('usa')->__('Product name is missing'));
        }
        $this->_page->drawText($name, $this->_x(8), $this->_y(12));
        $this->_page->restoreGS();
        return $this;
    }

    /**
     * Add Product Content Code
     *
     * @param string $code
     * @return $this
     * @throws InvalidArgumentException
     */
    public function addProductContentCode($code)
    {
        $this->_page->saveGS();
        $codes = [
            'TDK' => 0, 'TDE' => 1, 'TDL' => 0, 'TDM' => 1, 'TDT' => 0,
            'TDY' => 1, 'XPD' => 0, 'DOX' => 0, 'WPX' => 1, 'ECX' => 0,
            'DOM' => 0,
        ];
        if (!array_key_exists($code, $codes)) {
            throw new InvalidArgumentException(Mage::helper('usa')->__('Product content code is invalid'));
        }
        $font = null;
        if ($codes[$code]) {
            $this->_page->drawRectangle(
                $this->_x(140),
                $this->_y(0),
                $this->_x(190),
                $this->_y(28),
                Zend_Pdf_Page::SHAPE_DRAW_FILL,
            );
            $this->_page->setFillColor(new Zend_Pdf_Color_Html('#ffffff'));
            $font = $this->_fontBold;
        } else {
            $font = $this->_fontNormal;
        }
        $this->_page->setFont($font, 17);
        $this->_page->drawText($code, $this->_x(146), $this->_y(21));
        $this->_page->restoreGS();
        return $this;
    }

    /**
     * Add Unit Id
     *
     * @param int $id
     * @return $this
     */
    public function addUnitId($id)
    {
        $this->_page->saveGS();
        $this->_page->setFont($this->_fontNormal, 6);

        $this->_page->drawText('Unit ID', $this->_x(8), $this->_y(20));
        $this->_page->restoreGS();
        return $this;
    }

    /**
     * Add Reference Data
     *
     * @param $data
     * @return $this
     */
    public function addReferenceData($data)
    {
        $this->_page->saveGS();
        $this->_page->setFont($this->_fontNormal, 6);
        $this->_page->drawText('GREF', $this->_x(80), $this->_y(20));
        //TODO: Add reference data rendering
        $this->_page->restoreGS();
        return $this;
    }

    /**
     * Add Sender Info
     *
     * @return $this
     * @throws InvalidArgumentException
     */
    public function addSenderInfo(SimpleXMLElement $sender)
    {
        $this->_page->saveGS();
        $this->_page->setFont($this->_fontNormal, 6);
        $this->_page->drawText('From:', $this->_x(8), $this->_y(36));
        $contactName = implode(' ', array_filter([(string) $sender->CompanyName,
            (string) $sender->Contact->PersonName]));
        if (!$contactName) {
            throw new InvalidArgumentException(Mage::helper('usa')->__('Sender contact name is missing'));
        }
        $this->_page->drawText($contactName, $this->_x(25), $this->_y(36));

        $phoneNumber = implode(' ', array_filter([(string) $sender->Contact->PhoneNumber,
            (string) $sender->Contact->PhoneExtension]));
        $phoneNumber = $phoneNumber ? 'Phone: ' . $phoneNumber : '';
        $pageY = $this->_drawSenderAddress($sender->AddressLine, $phoneNumber);

        $divisionCode = (string) (strlen($sender->DivisionCode) ? $sender->DivisionCode . ' ' : null);
        $cityInfo = implode(' ', array_filter([$sender->City, $divisionCode, $sender->PostalCode]));
        if (!strlen($cityInfo)) {
            throw new InvalidArgumentException(Mage::helper('usa')->__('Sender city info is missing'));
        }
        $this->_page->drawText($cityInfo, $this->_x(25), $pageY);

        $this->_page->setFont($this->_fontBold, 6);
        $countryInfo = (string) (($sender->CountryName) ? $sender->CountryName : $sender->CountryCode);
        if (!strlen($countryInfo)) {
            throw new InvalidArgumentException(Mage::helper('usa')->__('Sender country info is missing'));
        }
        $this->_page->drawText($countryInfo, $this->_x(25), $pageY - $this->_page->getFontSize());

        $this->_page->restoreGS();
        return $this;
    }

    /**
     * Draw Sender Address
     *
     * @param string $phoneNumber
     * @return float
     */
    protected function _drawSenderAddress(SimpleXMLElement $addressLines, $phoneNumber)
    {
        $lines = [];
        foreach ($addressLines as $line) {
            $lines [] = $line;
        }

        $pageY = 0;
        if (strlen($lines[0]) > 28) {
            $firstLine = array_shift($lines);
            $pageY = $this->_page->drawLines([$firstLine], $this->_x(25), $this->_y(42), 28);
            $this->_page->drawText($phoneNumber, $this->_x(103), $this->_y(42));
        } else {
            $pageY = $this->_y(42);
            $lineLength = $this->_page->getTextWidth(
                $lines[0] . ' ',
                $this->_page->getFont(),
                $this->_page->getFontSize(),
            );
            $this->_page->drawText($phoneNumber, $this->_x(25 + $lineLength), $this->_y(42));
        }

        return $this->_page->drawLines($lines, $this->_x(25), $pageY, 49);
    }

    /**
     * Add Origin Info
     *
     * @param string $serviceAreaCode
     * @return $this
     * @throws InvalidArgumentException
     */
    public function addOriginInfo($serviceAreaCode)
    {
        if (strlen(!$serviceAreaCode)) {
            throw new InvalidArgumentException(Mage::helper('usa')->__('Origin serviceAreaCode is missing'));
        }
        $this->_page->saveGS();
        $this->_page->setFont($this->_fontNormal, 6);
        $this->_page->drawText('Origin:', $this->_x(260), $this->_y(36));
        $this->_page->setFont($this->_fontBold, 9);
        $this->_page->drawText($serviceAreaCode, $this->_x(260), $this->_y(45));

        $this->_page->restoreGS();
        return $this;
    }

    /**
     * Add Receive Info
     *
     * @return $this
     */
    public function addReceiveInfo(SimpleXMLElement $consignee)
    {
        $this->_page->saveGS();

        $this->_page->setFont($this->_fontNormal, 9);
        $this->_page->drawText('To:', $this->_x(5), $this->_y(92));
        $this->_page->drawText($consignee->CompanyName, $this->_x(20), $this->_y(90));
        $y = $this->_page->drawLines($consignee->AddressLine, $this->_x(19), $this->_y(100), 50);

        $this->_page->setFont($this->_fontBold, 11);
        $cityInfo = implode(' ', array_filter([$consignee->PostalCode, $consignee->City,
            $consignee->DivisionCode]));
        $y = min($y - 3, 460);
        $this->_page->drawLines([$cityInfo, $consignee->CountryName], $this->_x(20), $y, 44);

        $this->_page->setFont($this->_fontNormal, 6);
        $this->_page->drawText('Contact:', $this->_x(260), $this->_y(90));

        $y = $this->_page->drawLines(
            [$consignee->Contact->PersonName],
            $this->_x(283),
            $this->_y(98),
            25,
            Mage_Usa_Model_Shipping_Carrier_Dhl_Label_Pdf_Page::ALIGN_RIGHT,
        );
        $phoneNumber = implode(' ', array_filter([$consignee->Contact->PhoneNumber,
            $consignee->Contact->PhoneExtension]));
        $this->_page->drawText(
            $phoneNumber,
            $this->_x(283),
            $y,
            'UTF-8',
            Mage_Usa_Model_Shipping_Carrier_Dhl_Label_Pdf_Page::ALIGN_RIGHT,
        );

        $this->_page->restoreGS();
        return $this;
    }

    /**
     * Add Destination Facility Code
     *
     * @param string $countryCode
     * @param string $serviceAreaCode
     * @param string $facilityCode
     * @return $this
     * @throws InvalidArgumentException
     */
    public function addDestinationFacilityCode($countryCode, $serviceAreaCode, $facilityCode)
    {
        $this->_page->saveGS();
        $this->_page->setFont($this->_fontNormal, 20);
        $code = implode('-', array_filter([$countryCode, $serviceAreaCode, $facilityCode]));

        if (!strlen($code)) {
            throw new InvalidArgumentException(Mage::helper('usa')->__('Destination facility code is empty'));
        }
        $this->_page->drawText(
            $code,
            $this->_x(144),
            $this->_y(186),
            'UTF-8',
            Mage_Usa_Model_Shipping_Carrier_Dhl_Label_Pdf_Page::ALIGN_CENTER,
        );

        $this->_page->restoreGS();
        return $this;
    }

    /**
     * Add Service Features Codes
     *
     * @return $this
     */
    public function addServiceFeaturesCodes()
    {
        $this->_page->saveGS();
        $this->_page->drawRectangle(
            $this->_x(0),
            $this->_y(195),
            $this->_x(218),
            $this->_y(217),
            Zend_Pdf_Page::SHAPE_DRAW_FILL,
        );
        $this->_page->restoreGS();
        return $this;
    }

    /**
     * Add Delivery Date Code
     *
     * @return $this
     */
    public function addDeliveryDateCode()
    {
        $this->_page->saveGS();

        $this->_page->setFont($this->_fontNormal, 6);
        $this->_page->drawText('Day:', $this->_x(220), $this->_y(201));
        $this->_page->drawText('Time:', $this->_x(250), $this->_y(201));

        $this->_page->setFont($this->_fontNormal, 20);

        $this->_page->restoreGS();
        return $this;
    }

    /**
     * Add Shipment Information
     *
     * @param Mage_Sales_Model_Order_Shipment $data
     * @return $this
     * @throws InvalidArgumentException
     */
    public function addShipmentInformation($data)
    {
        $this->_page->saveGS();
        $this->_page->setFont($this->_fontNormal, 6);

        $refCode = $data->getOrder()->getIncrementId();
        if (!$refCode) {
            throw new InvalidArgumentException(Mage::helper('usa')->__('Reference code is missing'));
        }
        $this->_page->drawText(
            'Ref Code: ' . Mage::helper('usa')->__('Order #%s', $refCode),
            $this->_x(8),
            $this->_y(224),
        );
        $this->_page->restoreGS();
        return $this;
    }

    /**
     * Add Date Info
     * @param string $date
     * @return $this
     */
    public function addDateInfo($date)
    {
        $this->_page->saveGS();

        $this->_page->setFont($this->_fontNormal, 6);
        $this->_page->drawText('Date:', $this->_x(160), $this->_y(224));
        $this->_page->drawText($date, $this->_x(150), $this->_y(231));

        $this->_page->restoreGS();
        return $this;
    }

    /**
     * Add Weight Info
     *
     * @param string $weight
     * @param string $unit
     * @return $this
     * @throws InvalidArgumentException
     */
    public function addWeightInfo($weight, $unit)
    {
        $this->_page->saveGS();

        $units = ['K' => 'kg', 'L' => 'lb'];
        if (!isset($units[$unit])) {
            throw new InvalidArgumentException(Mage::helper('usa')->__('Weight unit is invalid'));
        }
        $unit = $units[$unit];

        $this->_page->setFont($this->_fontNormal, 6);
        $this->_page->drawText('Shpt Weight:', $this->_x(196), $this->_y(224));
        $this->_page->setFont($this->_fontBold, 11);
        $this->_page->drawText($weight . ' ' . $unit, $this->_x(195), $this->_y(234));

        $this->_page->restoreGS();
        return $this;
    }

    /**
     * Add Content: Shipment Description
     *
     * @param array $package
     * @return $this
     * @throws InvalidArgumentException
     */
    public function addContentInfo($package)
    {
        $this->_page->saveGS();
        $this->_page->setFont($this->_fontNormal, 6);
        if (empty($package)) {
            throw new InvalidArgumentException(Mage::helper('usa')->__('Package content is missing'));
        }

        $x = 225;
        $y = 300;
        $this->_page->drawText('Content: ', $this->_x($x), $this->_y($y));
        $i = 0;
        foreach ($package['items'] as $item) {
            $i++;
            $this->_page->drawText(substr($item['name'], 0, 20), $this->_x($x), $this->_y($y += 6));
            if ($i == 12) {
                break;
            }
        }
        $this->_page->restoreGS();
        return $this;
    }

    /**
     * Add Waybill Barcode
     *
     * @param string $number
     * @param string $barCode
     * @return $this
     * @throws InvalidArgumentException
     */
    public function addWaybillBarcode($number, $barCode)
    {
        $this->_page->saveGS();

        if (!strlen($number) || !strlen($barCode)) {
            throw new InvalidArgumentException(Mage::helper('usa')->__('Waybill barcode information is missing'));
        }
        $image = new Zend_Pdf_Resource_Image_Png('data://image/png;base64,' . $barCode);
        $this->_page->drawImage($image, $this->_x(0), $this->_y(296), $this->_x(232), $this->_y(375));

        $this->_page->setFont($this->_fontNormal, 9);
        $number = substr($number, 0, 2) . ' ' . substr($number, 2, 4) . ' ' . substr($number, 6, 4);
        $this->_page->drawText('WAYBILL ' . $number, $this->_x(13.5), $this->_y(382));

        $this->_page->restoreGS();
        return $this;
    }

    /**
     * Add Routing Barcode
     *
     * @param string $routingCode
     * @param string $id
     * @param string $barCode
     * @return $this
     * @throws InvalidArgumentException
     */
    public function addRoutingBarcode($routingCode, $id, $barCode)
    {
        $this->_page->saveGS();

        if (!$barCode) {
            throw new InvalidArgumentException(Mage::helper('usa')->__('Routing barcode is missing'));
        }

        $image = new Zend_Pdf_Resource_Image_Png('data://image/png;base64,' . $barCode);
        $this->_page->drawImage($image, $this->_x(0), $this->_y(386), $this->_x(232), $this->_y(465));

        $this->_page->setFont($this->_fontNormal, 9);
        $routingText = '(' . $id . ')' . $routingCode;
        $this->_page->drawText($routingText, $this->_x(12), $this->_y(472));

        $this->_page->restoreGS();
        return $this;
    }

    /**
     * Add Piece Id Barcode
     *
     * @param string $dataIdentifier
     * @param string $licensePlate
     * @param string $barCode
     * @return $this
     * @throws InvalidArgumentException
     */
    public function addPieceIdBarcode($dataIdentifier, $licensePlate, $barCode)
    {
        $this->_page->saveGS();

        if (!strlen($barCode)) {
            throw new InvalidArgumentException(Mage::helper('usa')->__('Piece Id barcode is missing'));
        }

        $image = new Zend_Pdf_Resource_Image_Png('data://image/png;base64,' . $barCode);
        $this->_page->drawImage($image, $this->_x(29), $this->_y(476), $this->_x(261), $this->_y(555));

        $this->_page->setFont($this->_fontNormal, 9);
        $routingText = '(' . $dataIdentifier . ')' . $licensePlate;
        $this->_page->drawText(
            $routingText,
            $this->_x(144),
            $this->_y(563),
            'UTF-8',
            Mage_Usa_Model_Shipping_Carrier_Dhl_Label_Pdf_Page::ALIGN_CENTER,
        );

        $this->_page->restoreGS();
        return $this;
    }

    /**
     * Add Piece Number
     *
     * @param int $pieceNumber
     * @param int $piecesTotal
     * @return $this
     * @throws InvalidArgumentException
     */
    public function addPieceNumber($pieceNumber, $piecesTotal)
    {
        $this->_page->saveGS();

        if (!$pieceNumber || !$piecesTotal) {
            throw new InvalidArgumentException(Mage::helper('usa')->__('Piece number information is missing'));
        }

        $this->_page->setFont($this->_fontNormal, 6);
        $this->_page->drawText('Piece:', $this->_x(256), $this->_y(224));
        $this->_page->setFont($this->_fontBold, 11);
        $this->_page->drawText($pieceNumber . '/' . $piecesTotal, $this->_x(256), $this->_y(234));

        $this->_page->restoreGS();
        return $this;
    }
}
