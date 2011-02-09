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
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_XmlConnect_Helper_Ipad extends Mage_Core_Helper_Abstract
{
    /**
     * Ipad landscape orientation identificator
     *
     * @var string
     */
    const ORIENTATION_LANDSCAPE = 'landscape';

    /**
     * Ipad portrait orientation identificator
     *
     * @var string
     */
    const ORIENTATION_PORTRAIT = 'portrait';

    /**
     * Ipad preview banner widht
     *
     * @var int
     */
    const PREVIEW_BANNER_WIDTH = 350;

    /**
     * Ipad preview banner image height
     *
     * @var int
     */
    const PREVIEW_BANNER_HEIGHT = 135;

    /**
     * Ipad landscape orientation preview image widht
     *
     * @var int
     */
    const PREVIEW_LANDSCAPE_BACKGROUND_WIDTH = 467;

    /**
     * Ipad landscape orientation preview image height
     *
     * @var int
     */
    const PREVIEW_LANDSCAPE_BACKGROUND_HEIGHT = 321;

    /**
     * Ipad portrait orientation preview image widht
     *
     * @var int
     */
    const PREVIEW_PORTRAIT_BACKGROUND_WIDTH = 350;

    /**
     * Ipad portrait orientation preview image height
     *
     * @var int
     */
    const PREVIEW_PORTRAIT_BACKGROUND_HEIGHT = 438;

    /**
     * Get default application tabs
     *
     * @param string
     * @return array
     */
    public function getDefaultDesignTabs()
    {
        if (!isset($this->_tabs)) {
            $this->_tabs = array(
                array(
                    'label' => Mage::helper('xmlconnect')->__('Home'),
                    'image' => 'tab_home.png',
                    'action' => 'Home',
                ),
                array(
                    'label' => Mage::helper('xmlconnect')->__('Shop'),
                    'image' => 'tab_shop.png',
                    'action' => 'Shop',
                ),
                array(
                    'label' => Mage::helper('xmlconnect')->__('Search'),
                    'image' => 'tab_search.png',
                    'action' => 'Search',
                ),
                array(
                    'label' => Mage::helper('xmlconnect')->__('Cart'),
                    'image' => 'tab_cart.png',
                    'action' => 'Cart',
                ),
                array(
                    'label' => Mage::helper('xmlconnect')->__('Account'),
                    'image' => 'tab_account_ipad.png',
                    'action' => 'Account',
                ),
                array(
                    'label' => Mage::helper('xmlconnect')->__('More Info'),
                    'image' => 'tab_page.png',
                    'action' => 'AboutUs',
                ),
            );
        }
        return $this->_tabs;
    }

    /**
     * Default application configuration
     *
     * @return array
     */
     public function getDefaultConfiguration()
     {
        return array(
            'native' => array(
                'body' => array(
                    'backgroundColor' => '#ABABAB',
                    'scrollBackgroundColor' => '#EDEDED',
                ),
                'itemActions' => array(
                    'relatedProductBackgroundColor' => '#404040',
                ),
                'fonts' => array(
                    'Title1' => array(
                        'name' => 'HelveticaNeue-Bold',
                        'size' => '20',
                        'color' => '#FEFEFE',
                    ),
                    'Title2' => array(
                        'name' => 'HelveticaNeue-Bold',
                        'size' => '15',
                        'color' => '#222222',
                    ),
                    'Title3' => array(
                        'name' => 'HelveticaNeue',
                        'size' => '14',
                        'color' => '#222222',
                    ),
                    'Title4' => array(
                        'name' => 'HelveticaNeue',
                        'size' => '12',
                        'color' => '#FFFFFF',
                    ),
                    'Title5' => array(
                        'name' => 'HelveticaNeue-Bold',
                        'size' => '18',
                        'color' => '#d55000',
                    ),
                    'Title6' => array(
                        'name' => 'HelveticaNeue-Bold',
                        'size' => '16',
                        'color' => '#FFFFFF',
                    ),
                    'Title7' => array(
                        'name' => 'HelveticaNeue-Bold',
                        'size' => '13',
                        'color' => '#222222',
                    ),
                    'Title8' => array(
                        'name' => 'HelveticaNeue-Bold',
                        'size' => '13',
                        'color' => '#FFFFFF',
                    ),
                    'Title9' => array(
                        'name' => 'HelveticaNeue-Bold',
                        'size' => '18',
                        'color' => '#FFFFFF',
                    ),
                    'Text1' => array(
                        'name' => 'HelveticaNeue-Bold',
                        'size' => '14',
                        'color' => '#222222',
                    ),
                    'Text2' => array(
                        'name' => 'HelveticaNeue',
                        'size' => '12',
                        'color' => '#222222',
                    ),
                ),
            ),
        );
     }

    /**
     * List of allowed fonts for iPad application
     *
     * @return array
     */
    public function getFontList()
    {
        return array(
            array(
                'value' => 'HiraKakuProN-W3',
                'label' => 'HiraKakuProN-W3',
            ),
            array(
                'value' => 'Courier',
                'label' => 'Courier',
            ),
            array(
                'value' => 'Courier-BoldOblique',
                'label' => 'Courier-BoldOblique',
            ),
            array(
                'value' => 'Courier-Oblique',
                'label' => 'Courier-Oblique',
            ),
            array(
                'value' => 'Courier-Bold',
                'label' => 'Courier-Bold',
            ),
            array(
                'value' => 'ArialMT',
                'label' => 'ArialMT',
            ),
            array(
                'value' => 'Arial-BoldMT',
                'label' => 'Arial-BoldMT',
            ),
            array(
                'value' => 'Arial-BoldItalicMT',
                'label' => 'Arial-BoldItalicMT',
            ),
            array(
                'value' => 'Arial-ItalicMT',
                'label' => 'Arial-ItalicMT',
            ),
            array(
                'value' => 'STHeitiTC-Light',
                'label' => 'STHeitiTC-Light',
            ),
            array(
                'value' => 'STHeitiTC-Medium',
                'label' => 'STHeitiTC-Medium',
            ),
            array(
                'value' => 'AppleGothic',
                'label' => 'AppleGothic',
            ),
            array(
                'value' => 'CourierNewPS-BoldMT',
                'label' => 'CourierNewPS-BoldMT',
            ),
            array(
                'value' => 'CourierNewPS-ItalicMT',
                'label' => 'CourierNewPS-ItalicMT',
            ),
            array(
                'value' => 'CourierNewPS-BoldItalicMT',
                'label' => 'CourierNewPS-BoldItalicMT',
            ),
            array(
                'value' => 'CourierNewPSMT',
                'label' => 'CourierNewPSMT',
            ),
            array(
                'value' => 'Zapfino',
                'label' => 'Zapfino',
            ),
            array(
                'value' => 'HiraKakuProN-W6',
                'label' => 'HiraKakuProN-W6',
            ),
            array(
                'value' => 'ArialUnicodeMS',
                'label' => 'ArialUnicodeMS',
            ),
            array(
                'value' => 'STHeitiSC-Medium',
                'label' => 'STHeitiSC-Medium',
            ),
            array(
                'value' => 'STHeitiSC-Light',
                'label' => 'STHeitiSC-Light',
            ),
            array(
                'value' => 'AmericanTypewriter',
                'label' => 'AmericanTypewriter',
            ),
            array(
                'value' => 'AmericanTypewriter-Bold',
                'label' => 'AmericanTypewriter-Bold',
            ),
            array(
                'value' => 'Helvetica-Oblique',
                'label' => 'Helvetica-Oblique',
            ),
            array(
                'value' => 'Helvetica-BoldOblique',
                'label' => 'Helvetica-BoldOblique',
            ),
            array(
                'value' => 'Helvetica',
                'label' => 'Helvetica',
            ),
            array(
                'value' => 'Helvetica-Bold',
                'label' => 'Helvetica-Bold',
            ),
            array(
                'value' => 'MarkerFelt-Thin',
                'label' => 'MarkerFelt-Thin',
            ),
            array(
                'value' => 'HelveticaNeue',
                'label' => 'HelveticaNeue',
            ),
            array(
                'value' => 'HelveticaNeue-Bold',
                'label' => 'HelveticaNeue-Bold',
            ),
            array(
                'value' => 'DBLCDTempBlack',
                'label' => 'DBLCDTempBlack',
            ),
            array(
                'value' => 'Verdana-Bold',
                'label' => 'Verdana-Bold',
            ),
            array(
                'value' => 'Verdana-BoldItalic',
                'label' => 'Verdana-BoldItalic',
            ),
            array(
                'value' => 'Verdana',
                'label' => 'Verdana',
            ),
            array(
                'value' => 'Verdana-Italic',
                'label' => 'Verdana-Italic',
            ),
            array(
                'value' => 'TimesNewRomanPSMT',
                'label' => 'TimesNewRomanPSMT',
            ),
            array(
                'value' => 'TimesNewRomanPS-BoldMT',
                'label' => 'TimesNewRomanPS-BoldMT',
            ),
            array(
                'value' => 'TimesNewRomanPS-BoldItalicMT',
                'label' => 'TimesNewRomanPS-BoldItalicMT',
            ),
            array(
                'value' => 'TimesNewRomanPS-ItalicMT',
                'label' => 'TimesNewRomanPS-ItalicMT',
            ),
            array(
                'value' => 'Georgia-Bold',
                'label' => 'Georgia-Bold',
            ),
            array(
                'value' => 'Georgia',
                'label' => 'Georgia',
            ),
            array(
                'value' => 'Georgia-BoldItalic',
                'label' => 'Georgia-BoldItalic',
            ),
            array(
                'value' => 'Georgia-Italic',
                'label' => 'Georgia-Italic',
            ),
            array(
                'value' => 'STHeitiJ-Medium',
                'label' => 'STHeitiJ-Medium',
            ),
            array(
                'value' => 'STHeitiJ-Light',
                'label' => 'STHeitiJ-Light',
            ),
            array(
                'value' => 'ArialRoundedMTBold',
                'label' => 'ArialRoundedMTBold',
            ),
            array(
                'value' => 'TrebuchetMS-Italic',
                'label' => 'TrebuchetMS-Italic',
            ),
            array(
                'value' => 'TrebuchetMS',
                'label' => 'TrebuchetMS',
            ),
            array(
                'value' => 'Trebuchet-BoldItalic',
                'label' => 'Trebuchet-BoldItalic',
            ),
            array(
                'value' => 'TrebuchetMS-Bold',
                'label' => 'TrebuchetMS-Bold',
            ),
            array(
                'value' => 'STHeitiK-Medium',
                'label' => 'STHeitiK-Medium',
            ),
            array(
                'value' => 'STHeitiK-Light',
                'label' => 'STHeitiK-Light',
            ),
        );
    }

    /**
     * List of allowed font sizes for iPad application
     *
     * @return array
     */
    public function getFontSizes()
    {
        $result = array( );
        for ($i = 6; $i < 32; $i++) {
            $result[] = array(
                'value' => $i,
                'label' => $i . ' pt',
            );
        }
        return $result;
    }

    /**
     * Get list of coutries that allowed in Ituens by Apple Store for Ipad
     * (get info from Iphone helper)
     *
     * @return array
     */
    public function getItunesCountriesArray()
    {
        return Mage::helper('xmlconnect/iphone')->getItunesCountriesArray();
    }
}
