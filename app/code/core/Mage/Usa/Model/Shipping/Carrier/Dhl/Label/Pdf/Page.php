<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Usa
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * DHL International (API v1.4) Label Creation
 *
 * @category   Mage
 * @package    Mage_Usa
 * @deprecated now the process of creating the label is on DHL side
 */
class Mage_Usa_Model_Shipping_Carrier_Dhl_Label_Pdf_Page extends Zend_Pdf_Page
{
    /**
     * Text align constants
     */
    public const ALIGN_RIGHT = 'right';
    public const ALIGN_LEFT = 'left';
    public const ALIGN_CENTER = 'center';

    /**
     * Dhl International Label Creation Class Pdf Page constructor
     * Create/Make a copy of pdf page
     *
     * @param Mage_Usa_Model_Shipping_Carrier_Dhl_Label_Pdf_Page|string $param1
     * @param mixed $param2
     * @param mixed $param3
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
     * @param float $fontSize
     * @return float
     */
    public function getTextWidth($text, Zend_Pdf_Resource_Font $font, $fontSize)
    {
        $drawingText = iconv('', 'UTF-16BE', $text);
        $characters = [];
        for ($i = 0; $i < strlen($drawingText); $i++) {
            $characters[] = (ord($drawingText[$i++]) << 8) | ord($drawingText[$i]);
        }
        $glyphs = $font->glyphNumbersForCharacters($characters);
        $widths = $font->widthsForGlyphs($glyphs);
        return (array_sum($widths) / $font->getUnitsPerEm()) * $fontSize;
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
     * @return Zend_Pdf_Canvas_Interface
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
