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
 * @package     Mage_XmlConnect
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * XmlConnect device helper for iPad
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Helper_Ipad extends Mage_XmlConnect_Helper_Device_Abstract
{
    /**
     * Submission title length
     */
    const SUBMISSION_TITLE_LENGTH = 200;

    /**
     * Submission description length
     */
    const SUBMISSION_DESCRIPTION_LENGTH = 500;

    /**
     * Ipad landscape orientation identifier
     * @deprecated
     */
    const ORIENTATION_LANDSCAPE = 'landscape';

    /**
     * Ipad portrait orientation identifier
     * @deprecated
     */
    const ORIENTATION_PORTRAIT = 'portrait';

    /**
     * Ipad portrait preview banner width
     * @deprecated
     */
    const PREVIEW_PORTRAIT_BANNER_WIDTH = 350;

    /**
     * Ipad portrait preview banner image height
     * @deprecated
     */
    const PREVIEW_PORTRAIT_BANNER_HEIGHT = 135;

    /**
     * Ipad landscape preview banner width
     * @deprecated
     */
    const PREVIEW_LANDSCAPE_BANNER_WIDTH = 467;

    /**
     * Ipad landscape preview banner image height
     * @deprecated
     */
    const PREVIEW_LANDSCAPE_BANNER_HEIGHT = 157;

    /**
     * Ipad landscape orientation preview image width
     * @deprecated
     */
    const PREVIEW_LANDSCAPE_BACKGROUND_WIDTH = 467;

    /**
     * Ipad landscape orientation preview image height
     * @deprecated
     */
    const PREVIEW_LANDSCAPE_BACKGROUND_HEIGHT = 321;

    /**
     * Ipad portrait orientation preview image width
     * @deprecated
     */
    const PREVIEW_PORTRAIT_BACKGROUND_WIDTH = 350;

    /**
     * Ipad portrait orientation preview image height
     * @deprecated
     */
    const PREVIEW_PORTRAIT_BACKGROUND_HEIGHT = 438;

    /**
     * Submit images that are stored in "params" field of history table
     *
     * @var array
     */
    protected $_imageIds = array(
        'icon', 'ipad_loader_portrait_image', 'ipad_loader_landscape_image', 'ipad_logo', 'big_logo'
    );

    /**
     * Get default application tabs
     *
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
            );
        }
        return $this->_tabs;
    }

    /**
     * Default images list
     *
     * @return array
     */
     public function getImagesTypesList()
     {
         return array(
             Mage_XmlConnect_Model_Device_Ipad::IMAGE_TYPE_ICON => array(
                 'count' => Mage_XmlConnect_Model_Device_Ipad::IMAGE_TYPE_ICON_COUNT,
                 'label' => $this->__('Logo in Header'),
                 'sortable' => false,
                 'sizeTip' => $this->__('Recommended size %spx x %spx.', 35, 35)
             ),
             Mage_XmlConnect_Model_Device_Ipad::IMAGE_TYPE_PORTRAIT_BACKGROUND => array(
                 'count' => Mage_XmlConnect_Model_Device_Ipad::IMAGE_TYPE_BACKGROUND_COUNT,
                 'label' => $this->__('Portrait app Background'),
                 'sortable' => false,
                 'sizeTip' => $this->__('Recommended size %spx x %spx.', 768, 960) . $this->__('Note: Image size affects the performance of your app.')
             ),
             Mage_XmlConnect_Model_Device_Ipad::IMAGE_TYPE_LANDSCAPE_BACKGROUND => array(
                 'count' => Mage_XmlConnect_Model_Device_Ipad::IMAGE_TYPE_BACKGROUND_COUNT,
                 'label' => $this->__('Landscape app Background'),
                 'sortable' => false,
                 'sizeTip' => $this->__('Recommended size %spx x %spx.', 1024, 704) . $this->__('Note: Image size affects the performance of your app.')
             ),
             Mage_XmlConnect_Model_Device_Ipad::IMAGE_TYPE_PORTRAIT_BANNER => array(
                 'count' => Mage_XmlConnect_Model_Device_Ipad::IMAGE_TYPE_BANNER_COUNT,
                 'label' => $this->__('Portrait banners on Home Screen'),
                 'sortable' => true,
                 'sizeTip' => $this->__('Recommended size %spx x %spx.', 768, 294) . $this->__('Note: Image size affects the performance of your app.')
             ),
             Mage_XmlConnect_Model_Device_Ipad::IMAGE_TYPE_LANDSCAPE_BANNER => array(
                 'count' => Mage_XmlConnect_Model_Device_Ipad::IMAGE_TYPE_BANNER_COUNT,
                 'label' => $this->__('Landscape banners on Home Screen'),
                 'sortable' => true,
                 'sizeTip' => $this->__('Recommended size %spx x %spx.', 1024, 344) . $this->__('Note: Image size affects the performance of your app.')
         ));
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
         ))));
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
        ));
    }


    /**
     * Get list of countries that allowed in Itunes by Apple Store for Ipad
     * (we get info from Iphone helper)
     *
     * @deprecated
     * @see $this->getAllowedCountriesArray()
     * @return array
     */
    public function getItunesCountriesArray()
    {
        return $this->_allowedCountries;
    }

    /**
     * Validate submit application data
     *
     * @param array $params
     * @return array
     */
    public function validateSubmit($params)
    {
        $errors = array();

        if (!Zend_Validate::is(isset($params['title']) ? $params['title'] : null, 'NotEmpty')) {
            $errors[] = Mage::helper('xmlconnect')->__('Please enter the Title.');
        }

        if (isset($params['title'])) {
            $titleLength = self::SUBMISSION_TITLE_LENGTH;
            $strRules = array('min' => '1', 'max' => $titleLength);
            if (!Zend_Validate::is($params['title'], 'StringLength', $strRules)) {
                $errors[] = Mage::helper('xmlconnect')->__('"Title" is more than %d characters long', $strRules['max']);
            }
        }

        if (!Zend_Validate::is(isset($params['description']) ? $params['description'] : null, 'NotEmpty')) {
            $errors[] = Mage::helper('xmlconnect')->__('Please enter the Description.');
        }

        if (isset($params['description'])) {
            $descriptionLength = self::SUBMISSION_DESCRIPTION_LENGTH;
            $strRules = array('min' => '1', 'max' => $descriptionLength);
            if (!Zend_Validate::is($params['title'], 'StringLength', $strRules)) {
                $errors[] = Mage::helper('xmlconnect')->__('"Description" is more than %d characters long', $strRules['max']);
            }
        }

        if (!Zend_Validate::is(isset($params['copyright']) ? $params['copyright'] : null, 'NotEmpty')) {
            $errors[] = Mage::helper('xmlconnect')->__('Please enter the Copyright.');
        }

        if (empty($params['price_free'])) {
            if (!Zend_Validate::is(isset($params['price']) ? $params['price'] : null, 'NotEmpty')) {
                $errors[] = Mage::helper('xmlconnect')->__('Please enter the Price.');
            }
        }

        if (!Zend_Validate::is(isset($params['country']) ? $params['country'] : null, 'NotEmpty')) {
            $errors[] = Mage::helper('xmlconnect')->__('Please select at least one country.');
        }

        $keyLenght = Mage_XmlConnect_Model_Application::APP_MAX_KEY_LENGTH;
        if (Mage::helper('xmlconnect')->getApplication()->getIsResubmitAction()) {
            if (isset($params['resubmission_activation_key'])) {
                $resubmissionKey = $params['resubmission_activation_key'];
            } else {
                $resubmissionKey = null;
            }
            if (!Zend_Validate::is($resubmissionKey, 'NotEmpty')) {
                $errors[] = Mage::helper('xmlconnect')->__('Please enter the Resubmission Key.');
            } elseif (!Zend_Validate::is($resubmissionKey, 'StringLength', array(1, $keyLenght))) {
                $errors[] = Mage::helper('xmlconnect')->__('Submit App failure. Invalid activation key provided');
            }
        } else {
            $key = isset($params['key']) ? $params['key'] : null;
            if (!Zend_Validate::is($key, 'NotEmpty')) {
                $errors[] = Mage::helper('xmlconnect')->__('Please enter the Activation Key.');
            } elseif (!Zend_Validate::is($key, 'StringLength', array(1, $keyLenght))) {
                $errors[] = Mage::helper('xmlconnect')->__('Submit App failure. Invalid activation key provided');
            }
        }
        return $errors;
    }

    /**
     * Check config for valid values
     *
     * @deprecated we don't use it since 1.11.1.0
     * @param array $native
     * @return array
     */
    public function validateConfig($native)
    {
        $errors = array();
        if ($native === false
            || (!isset($native['navigationBar']['icon'])
                || !Zend_Validate::is($native['navigationBar']['icon'], 'NotEmpty')
            )
        ) {
            $errors[] = Mage::helper('xmlconnect')->__('Please upload  an image for "Logo in Header" field from Design Tab.');
        }

        if (!Mage::helper('xmlconnect')->validateConfFieldNotEmpty('bannerIpadLandscapeImage', $native)) {
            $errors[] = Mage::helper('xmlconnect')->__('Please upload  an image for "Banner on Home Screen (landscape mode)" field from Design Tab.');
        }

        if (!Mage::helper('xmlconnect')->validateConfFieldNotEmpty('bannerIpadImage', $native)) {
            $errors[] = Mage::helper('xmlconnect')->__('Please upload  an image for "Banner on Home Screen (portrait mode)" field from Design Tab.');
        }

        if (!Mage::helper('xmlconnect')->validateConfFieldNotEmpty('backgroundIpadLandscapeImage', $native)) {
            $errors[] = Mage::helper('xmlconnect')->__('Please upload  an image for "App Background (landscape mode)" field from Design Tab.');
        }

        if (!Mage::helper('xmlconnect')->validateConfFieldNotEmpty('backgroundIpadPortraitImage', $native)) {
            $errors[] = Mage::helper('xmlconnect')->__('Please upload  an image for "App Background (portrait mode)" field from Design Tab.');
        }

        return $errors;
    }

    /**
     * Check image fields
     *
     * We set empty value for image field if file was missed in some reason
     *
     * @deprecated will delete in the next version
     * @param array $data
     * @return array
     */
    public function checkImages(array $data)
    {
        /** @var $helper Mage_XmlConnect_Helper_Image */
        $helper = Mage::helper('xmlconnect/image');

        $icon =& $data['conf']['native']['navigationBar']['icon'];

        if (!empty($icon) && !$helper->checkAndGetImagePath($icon)) {
            $icon = '';
        }

        $bannerLand =& $data['conf']['native']['body']['bannerIpadLandscapeImage'];

        if (!empty($bannerLand) && !$helper->checkAndGetImagePath($bannerLand)) {
            $bannerLand = '';
        }

        $banner =& $data['conf']['native']['body']['bannerIpadImage'];

        if (!empty($banner) && !$helper->checkAndGetImagePath($banner)) {
            $banner = '';
        }

        $backgroundLand =& $data['conf']['native']['body']['backgroundIpadLandscapeImage'];

        if (!empty($backgroundLand) && !$helper->checkAndGetImagePath($backgroundLand)) {
            $backgroundLand = '';
        }

        $background =& $data['conf']['native']['body']['backgroundIpadPortraitImage'];

        if (!empty($background) && !$helper->checkAndGetImagePath($background)) {
            $background = '';
        }

        return $data;
    }

    /**
     * Check required fields of a config for a front-end
     *
     * @deprecated will delete in the next version
     * @throws Mage_Core_Exception
     * @param array $data
     * @return null
     */
    public function checkRequiredConfigFields($data)
    {
        if (!is_array($data)) {
            return;
        }

        if (isset($data['navigationBar']['icon']) && empty($data['navigationBar']['icon'])) {
            Mage::throwException(
                Mage::helper('xmlconnect')->__('Logo in Header image missing.')
            );
        }
        if (isset($data['body']['bannerIpadImage']) && empty($data['body']['bannerIpadImage'])) {
            Mage::throwException(
                Mage::helper('xmlconnect')->__('Banner on Home Screen (portrait mode) image missing.')
            );
        }
        if (isset($data['body']['bannerIpadLandscapeImage']) && empty($data['body']['bannerIpadLandscapeImage'])) {
            Mage::throwException(
                Mage::helper('xmlconnect')->__('Banner on Home Screen (landscape mode) image missing.')
            );
        }
        if (isset($data['body']['backgroundIpadLandscapeImage'])
            && empty($data['body']['backgroundIpadLandscapeImage'])
        ) {
            Mage::throwException(
                Mage::helper('xmlconnect')->__('App Background (landscape mode).')
            );
        }
        if (isset($data['body']['backgroundIpadPortraitImage'])
            && empty($data['body']['backgroundIpadPortraitImage'])
        ) {
            Mage::throwException(
                Mage::helper('xmlconnect')->__('App Background (portrait mode).')
            );
        }
    }
}
