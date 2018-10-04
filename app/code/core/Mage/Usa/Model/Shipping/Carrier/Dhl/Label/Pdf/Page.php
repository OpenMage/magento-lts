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
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
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
class Mage_Usa_Model_Shipping_Carrier_Dhl_Label_Pdf_Page extends Zend_Pdf_Page
{
    /**
     * Text align constants
     */
    const ALIGN_RIGHT = 'right';
    const ALIGN_LEFT = 'left';
    const ALIGN_CENTER = 'center';

    /**
     * Dhl International Label Creation Class Pdf Page constructor
     * Create/Make a copy of pdf page
     *
     * @param Mage_Usa_Model_Shipping_Carrier_Dhl_Label_Pdf_Page|string $param1
     * @param null $param2
     * @param null $param3
     */
    public function __construct($param1, $param2 = null, $param3 = null)
    {
        if ($param1 instanceof Mage_Usa_Model_Shipping_Carrier_Dhl_Label_Pdf_Page
            && $param2 === null && $param3 === null
        ) {
            $this->_contents = $param1->getContents();
        }
        parent::__construct($param1, $param2, $param3);
    }

    /**
     * Get PDF Page contents
     *
     * @return string
     */
    public function getContents()
    {
        return $this->_contents;
    }

    /**
     * Calculate the width of given text in points taking into account current font and font-size
     *
     * @param string $text
     * @param Zend_Pdf_Resource_Font $font
     * @param float $font_size
     * @return float
     */
    public function getTextWidth($text, Zend_Pdf_Resource_Font $font, $font_size)
    {
        $drawing_text = iconv('', 'UTF-16BE', $text);
        $characters = array();
        for ($i = 0; $i < strlen($drawing_text); $i++) {
            $characters[] = (ord($drawing_text[$i++]) << 8) | ord($drawing_text[$i]);
        }
        $glyphs = $font->glyphNumbersForCharacters($characters);
        $widths = $font->widthsForGlyphs($glyphs);
        $text_width = (array_sum($widths) / $font->getUnitsPerEm()) * $font_size;
        return $text_width;
    }

    /**
     * Draw a line of text at the specified position.
     *
     * @param string $text
     * @param float $x
     * @param float $y
     * @param string $charEncoding (optional) Character encoding of source text.
     *   Defaults to current locale.
     * @param $align
     * @throws Zend_Pdf_Exception
     * @return Mage_Usa_Model_Shipping_Carrier_Dhl_Label_Pdf_Page
     */
    public function drawText($text, $x, $y, $charEncoding = 'UTF-8', $align = self::ALIGN_LEFT)
    {
        $left = null;
        switch ($align) {
            case self::ALIGN_LEFT:
                $left = $x;
                break;

            case self::ALIGN_CENTER:
                $textWidth = $this->getTextWidth($text, $this->getFont(), $this->getFontSize());
                $left = $x - ($textWidth / 2);
                break;

            case self::ALIGN_RIGHT:
                $textWidth = $this->getTextWidth($text, $this->getFont(), $this->getFontSize());
                $left = $x - $textWidth;
                break;
        }
        return parent::drawText($text, $left, $y, $charEncoding);
    }

    /**
     * Draw a text paragraph taking into account the maximum number of symbols in a row.
     * If line is longer - spit it.
     *
     * @param array $lines
     * @param int $x
     * @param int $y
     * @param int $maxWidth - number of symbols
     * @param string $align
     * @throws Zend_Pdf_Exception
     * @return float
     */
    public function drawLines($lines, $x, $y, $maxWidth, $align = self::ALIGN_LEFT)
    {
        foreach ($lines as $line) {
            if (strlen($line) > $maxWidth) {
                $subLines = Mage::helper('core/string')->str_split($line, $maxWidth, true, true);
                $y = $this->drawLines(array_filter($subLines), $x, $y, $maxWidth, $align);
                continue;
            }
            $this->drawText($line, $x, $y, 'UTF-8', $align);
            $y -= ceil($this->getFontSize());
        }
        return $y;
    }
}
