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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Sales Order PDF abstract model
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Sales_Model_Order_Pdf_Abstract extends Varien_Object
{
    public $y;
    /**
     * Item renderers with render type key
     *
     * model    => the model name
     * renderer => the renderer model
     *
     * @var array
     */
    protected $_renderers = array();

    const XML_PATH_SALES_PDF_INVOICE_PUT_ORDER_ID = 'sales_pdf/invoice/put_order_id';
    const XML_PATH_SALES_PDF_SHIPMENT_PUT_ORDER_ID = 'sales_pdf/shipment/put_order_id';
    const XML_PATH_SALES_PDF_CREDITMEMO_PUT_ORDER_ID = 'sales_pdf/creditmemo/put_order_id';

    abstract public function getPdf();

    /**
     * Returns the total width in points of the string using the specified font and
     * size.
     *
     * This is not the most efficient way to perform this calculation. I'm
     * concentrating optimization efforts on the upcoming layout manager class.
     * Similar calculations exist inside the layout manager class, but widths are
     * generally calculated only after determining line fragments.
     *
     * @param string $string
     * @param Zend_Pdf_Resource_Font $font
     * @param float $fontSize Font size in points
     * @return float
     */
    public function widthForStringUsingFontSize($string, $font, $fontSize)
    {
        $drawingString = iconv('UTF-8', 'UTF-16BE//IGNORE', $string);
        $characters = array();
        for ($i = 0; $i < strlen($drawingString); $i++) {
            $characters[] = (ord($drawingString[$i++]) << 8) | ord($drawingString[$i]);
        }
        $glyphs = $font->glyphNumbersForCharacters($characters);
        $widths = $font->widthsForGlyphs($glyphs);
        $stringWidth = (array_sum($widths) / $font->getUnitsPerEm()) * $fontSize;
        return $stringWidth;

    }

    /**
     * Calculate coordinates to draw something in a column aligned to the right
     *
     * @param string $string
     * @param int $x
     * @param int $columnWidth
     * @param Zend_Pdf_Resource_Font $font
     * @param int $fontSize
     * @param int $padding
     * @return int
     */
    public function getAlignRight($string, $x, $columnWidth, Zend_Pdf_Resource_Font $font, $fontSize, $padding = 5)
    {
        $width = $this->widthForStringUsingFontSize($string, $font, $fontSize);
        return $x + $columnWidth - $width - $padding;
    }

    /**
     * Calculate coordinates to draw something in a column aligned to the center
     *
     * @param string $string
     * @param int $x
     * @param int $columnWidth
     * @param Zend_Pdf_Resource_Font $font
     * @param int $fontSize
     * @return int
     */
    public function getAlignCenter($string, $x, $columnWidth, Zend_Pdf_Resource_Font $font, $fontSize)
    {
        $width = $this->widthForStringUsingFontSize($string, $font, $fontSize);
        return $x + round(($columnWidth - $width) / 2);
    }

    protected function insertLogo(&$page)
    {
        $image = Mage::getStoreConfig('sales/identity/logo');
        if ($image) {
            $image = Mage::getStoreConfig('system/filesystem/media') . '/sales/store/logo/' . $image;
            if (is_file($image)) {

                $image = Zend_Pdf_Image::imageWithPath($image);
                $page->drawImage($image, 25, 800, 125, 825);
            }
        }
        //return $page;
    }

    protected function insertAddress(&$page)
    {
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->_setFontRegular($page, 5);

        $page->setLineWidth(0.5);
        $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
        $page->drawLine(125, 825, 125, 790);

        $page->setLineWidth(0);
        $this->y = 820;
        foreach (explode("\n", Mage::getStoreConfig('sales/identity/address')) as $value){
            if ($value!=='') {
                $page->drawText(trim(strip_tags($value)), 130, $this->y, 'UTF-8');
                $this->y -=7;
            }
        }
        //return $page;
    }

    protected function insertOrder(&$page, $order, $putOrderId = true)
    {

        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0.5));

        $page->drawRectangle(25, 790, 570, 755);

        $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
        $this->_setFontRegular($page);


        if ($putOrderId) {
            $page->drawText(Mage::helper('sales')->__('Order # ').$order->getRealOrderId(), 35, 770, 'UTF-8');
        }
        //$page->drawText(Mage::helper('sales')->__('Order Date: ') . date( 'D M j Y', strtotime( $order->getCreatedAt() ) ), 35, 760, 'UTF-8');
        $page->drawText(Mage::helper('sales')->__('Order Date: ') . Mage::helper('core')->formatDate($order->getCreatedAt(), 'medium', false), 35, 760, 'UTF-8');

        $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
        $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, 755, 275, 730);
        $page->drawRectangle(275, 755, 570, 730);

        /* Calculate blocks info */

        /* Billing Address */
        $billingAddress  = explode('|', $order->getBillingAddress()->format('pdf'));

        /* Payment */
        $paymentInfo = Mage::helper('payment')->getInfoBlock($order->getPayment())
            ->setIsSecureMode(true)
            ->toPdf();
        $payment = explode('{{pdf_row_separator}}', $paymentInfo);
        foreach ($payment as $key=>$value){
            if (strip_tags(trim($value))==''){
                unset($payment[$key]);
            }
        }
        reset($payment);

        /* Shipping Address and Method */
        if (!$order->getIsVirtual()) {
            /* Shipping Address */
            $shippingAddress = explode('|', $order->getShippingAddress()->format('pdf'));
            $shippingMethod  = $order->getShippingDescription();
        }

        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->_setFontRegular($page);
        $page->drawText(Mage::helper('sales')->__('SOLD TO:'), 35, 740 , 'UTF-8');

        if (!$order->getIsVirtual()) {
            $page->drawText(Mage::helper('sales')->__('SHIP TO:'), 285, 740 , 'UTF-8');
        }
        else {
            $page->drawText(Mage::helper('sales')->__('Payment Method:'), 285, 740 , 'UTF-8');
        }

        if (!$order->getIsVirtual()) {
            $y = 730-max(count($billingAddress), count($shippingAddress))*10+5;
        }
        else {
            $y = 730-count($billingAddress)*10 + 5;
        }

        $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
        $page->drawRectangle(25, 730, 570, $y);
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
		$this->_setFontRegular($page);
        $this->y = 720;

        foreach ($billingAddress as $value){
            if ($value!=='') {
                $page->drawText(strip_tags(ltrim($value)), 35, $this->y, 'UTF-8');
                $this->y -=10;
            }
        }

        if (!$order->getIsVirtual()) {
            $this->y = 720;
            foreach ($shippingAddress as $value){
                if ($value!=='') {
                    $page->drawText(strip_tags(ltrim($value)), 285, $this->y, 'UTF-8');
                    $this->y -=10;
                }

            }

            $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
            $page->setLineWidth(0.5);
            $page->drawRectangle(25, $this->y, 275, $this->y-25);
            $page->drawRectangle(275, $this->y, 570, $this->y-25);

            $this->y -=15;
            $this->_setFontBold($page);
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
            $page->drawText(Mage::helper('sales')->__('Payment Method'), 35, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Shipping Method:'), 285, $this->y , 'UTF-8');

            $this->y -=10;
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));

            $this->_setFontRegular($page);
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));

            $paymentLeft = 35;
            $yPayments   = $this->y - 15;
        }
        else {
            $yPayments   = 720;
            $paymentLeft = 285;
        }

        foreach ($payment as $value){
            if (trim($value)!=='') {
                $page->drawText(strip_tags(trim($value)), $paymentLeft, $yPayments, 'UTF-8');
                $yPayments -=10;
            }
        }

        if (!$order->getIsVirtual()) {
            $this->y -=15;

            $page->drawText($shippingMethod, 285, $this->y, 'UTF-8');

            $yShipments = $this->y;


            $totalShippingChargesText = "(" . Mage::helper('sales')->__('Total Shipping Charges') . " " . $order->formatPriceTxt($order->getBaseShippingAmount()) . ")";

            $page->drawText($totalShippingChargesText, 285, $yShipments-7, 'UTF-8');
            $yShipments -=10;
            $tracks = $order->getTracksCollection();
            if (count($tracks)) {
                $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
                $page->setLineWidth(0.5);
                $page->drawRectangle(285, $yShipments, 510, $yShipments - 10);
                $page->drawLine(380, $yShipments, 380, $yShipments - 10);
                //$page->drawLine(510, $yShipments, 510, $yShipments - 10);

                $this->_setFontRegular($page);
                $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
                //$page->drawText(Mage::helper('sales')->__('Carrier'), 290, $yShipments - 7 , 'UTF-8');
                $page->drawText(Mage::helper('sales')->__('Title'), 290, $yShipments - 7, 'UTF-8');
                $page->drawText(Mage::helper('sales')->__('Number'), 385, $yShipments - 7, 'UTF-8');

                $yShipments -=17;
                $this->_setFontRegular($page, 6);
                foreach ($order->getTracksCollection() as $track) {

                	$CarrierCode = $track->getCarrierCode();
                	if ($CarrierCode!='custom')
                	{
                		$carrier = Mage::getSingleton('shipping/config')->getCarrierInstance($CarrierCode);
                		$carrierTitle = $carrier->getConfigData('title');
                	}
                	else
                	{
                		$carrierTitle = Mage::helper('sales')->__('Custom Value');
                	}

                    $truncatedCarrierTitle = substr($carrierTitle, 0, 35) . (strlen($carrierTitle) > 35 ? '...' : '');
                    $truncatedTitle = substr($track->getTitle(), 0, 45) . (strlen($track->getTitle()) > 45 ? '...' : '');
                    //$page->drawText($truncatedCarrierTitle, 285, $yShipments , 'UTF-8');
                    $page->drawText($truncatedTitle, 300, $yShipments , 'UTF-8');
                    $page->drawText($track->getNumber(), 395, $yShipments , 'UTF-8');
                    $yShipments -=7;
                }
            } else {
                $yShipments -= 7;
            }

            $currentY = min($yPayments, $yShipments);

            // replacement of Shipments-Payments rectangle block
            $page->drawLine(25, $this->y + 15, 25, $currentY);
            $page->drawLine(25, $currentY, 570, $currentY);
            $page->drawLine(570, $currentY, 570, $this->y + 15);

            $this->y = $currentY;
            $this->y -= 15;
        }
    }

    protected function insertTotals(&$page, $source){
        $order = $source->getOrder();
        $font = $this->_setFontBold($page);

        $order_subtotal = Mage::helper('sales')->__('Order Subtotal:');
        $page->drawText($order_subtotal, 475-$this->widthForStringUsingFontSize($order_subtotal, $font, 7), $this->y, 'UTF-8');

        $order_subtotal = $order->formatPriceTxt($source->getSubtotal());
        $page->drawText($order_subtotal, 565-$this->widthForStringUsingFontSize($order_subtotal, $font, 7), $this->y, 'UTF-8');
        $this->y -=15;

        if ((float)$source->getDiscountAmount()){
            $discount = Mage::helper('sales')->__('Discount :');
            $page->drawText($discount, 475-$this->widthForStringUsingFontSize($discount, $font, 7), $this->y, 'UTF-8');

            $discount = $order->formatPriceTxt(0.00 - $source->getDiscountAmount());
            $page->drawText($discount, 565-$this->widthForStringUsingFontSize($discount, $font, 7), $this->y, 'UTF-8');
            $this->y -=15;
        }

        if ((float)$source->getTaxAmount()){
            $order_tax = Mage::helper('sales')->__('Tax :');
            $page->drawText($order_tax, 475-$this->widthForStringUsingFontSize($order_tax, $font, 7), $this->y, 'UTF-8');

            $order_tax = $order->formatPriceTxt($source->getTaxAmount());
            $page->drawText($order_tax, 565-$this->widthForStringUsingFontSize($order_tax, $font, 7), $this->y, 'UTF-8');
            $this->y -=15;
        }

        if ((float)$source->getShippingAmount()){
            $order_shipping = Mage::helper('sales')->__('Shipping & Handling:');
            $page->drawText($order_shipping, 475-$this->widthForStringUsingFontSize($order_shipping, $font, 7), $this->y, 'UTF-8');

            $order_shipping = $order->formatPriceTxt($source->getShippingAmount());
            $page->drawText($order_shipping, 565-$this->widthForStringUsingFontSize($order_shipping, $font, 7), $this->y, 'UTF-8');
            $this->y -=15;
        }

        if ($source->getAdjustmentPositive()){
            $adjustment_refund = Mage::helper('sales')->__('Adjustment Refund:');
            $page ->drawText($adjustment_refund, 475-$this->widthForStringUsingFontSize($adjustment_refund, $font, 7), $this->y, 'UTF-8');

            $adjustment_refund = $order->formatPriceTxt($source->getAdjustmentPositive());
            $page ->drawText($adjustment_refund, 565-$this->widthForStringUsingFontSize($adjustment_refund, $font, 7), $this->y, 'UTF-8');
            $this->y -=15;
        }

        if ((float) $source->getAdjustmentNegative()){
            $adjustment_fee = Mage::helper('sales')->__('Adjustment Fee:');
            $page ->drawText($adjustment_fee, 475-$this->widthForStringUsingFontSize($adjustment_fee, $font, 7), $this->y, 'UTF-8');

            $adjustment_fee=$order->formatPriceTxt($source->getAdjustmentNegative());
            $page ->drawText($adjustment_fee, 565-$this->widthForStringUsingFontSize($adjustment_fee, $font, 7), $this->y, 'UTF-8');
            $this->y -=15;
        }

        $page->setFont($font, 8);

        $order_grandtotal = Mage::helper('sales')->__('Grand Total:');
        $page ->drawText($order_grandtotal, 475-$this->widthForStringUsingFontSize($order_grandtotal, $font, 8), $this->y, 'UTF-8');

        $order_grandtotal = $order->formatPriceTxt($source->getGrandTotal());
        $page ->drawText($order_grandtotal, 565-$this->widthForStringUsingFontSize($order_grandtotal, $font, 8), $this->y, 'UTF-8');
        $this->y -=15;
    }

    protected function _parseItemDescription($item)
    {
        $description = $item->getDescription();
        if (preg_match_all('/<li.*?>(.*?)<\/li>/i', $description, $matches)) {
            return $matches[1];
        }

        return array($description);
    }

    protected function _beforeGetPdf() {
        $translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(false);
    }

    protected function _afterGetPdf() {
        $translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(true);
    }

    protected function _formatOptionValue($value, $order)
    {
        $resultValue = '';
        if (is_array($value)) {
            if (isset($value['qty'])) {
                $resultValue .= sprintf('%d', $value['qty']) . ' x ';
            }

            $resultValue .= $value['title'];

            if (isset($value['price'])) {
                $resultValue .= " " . $order->formatPrice($value['price']);
            }
            return  $resultValue;
        } else {
            return $value;
        }
    }

    protected function _initRenderer($type)
    {
        $node = Mage::getConfig()->getNode('global/pdf/'.$type);
        foreach ($node->children() as $renderer) {
            $this->_renderers[$renderer->getName()] = array(
                'model'     => (string)$renderer,
                'renderer'  => null
            );
        }
    }

    /**
     * Retrieve renderer model
     *
     * @throws Mage_Core_Exception
     * @return Mage_Sales_Model_Order_Pdf_Items_Abstract
     */
    protected function _getRenderer($type)
    {
        if (!isset($this->_renderers[$type])) {
            $type = 'default';
        }

        if (!isset($this->_renderers[$type])) {
            Mage::throwException(Mage::helper('sales')->__('Invalid renderer model'));
        }

        if (is_null($this->_renderers[$type]['renderer'])) {
            $this->_renderers[$type]['renderer'] = Mage::getSingleton($this->_renderers[$type]['model']);
        }

        return $this->_renderers[$type]['renderer'];
    }

    protected function _drawItem(Varien_Object $item, Zend_Pdf_Page $page, Mage_Sales_Model_Order $order)
    {
        $type = $item->getOrderItem()->getProductType();
        $renderer = $this->_getRenderer($type);
        $renderer->setOrder($order);
        $renderer->setItem($item);
        $renderer->setPdf($this);
        $renderer->setPage($page);

        $renderer->draw();
    }

    protected function _setFontRegular($object, $size = 7)
    {
        $font = Zend_Pdf_Font::fontWithPath(Mage::getBaseDir() . '/lib/LinLibertineFont/LinLibertineC_Re-2.8.0.ttf');
        $object->setFont($font, $size);
        return $font;
    }

    protected function _setFontBold($object, $size = 7)
    {
        $font = Zend_Pdf_Font::fontWithPath(Mage::getBaseDir() . '/lib/LinLibertineFont/LinLibertine_Bd-2.8.1.ttf');
        $object->setFont($font, $size);
        return $font;
    }

    protected function _setFontItalic($object, $size = 7)
    {
        $font = Zend_Pdf_Font::fontWithPath(Mage::getBaseDir() . '/lib/LinLibertineFont/LinLibertine_It-2.8.2.ttf');
        $object->setFont($font, $size);
        return $font;
    }
}