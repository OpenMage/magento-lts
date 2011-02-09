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
class Mage_XmlConnect_Block_Adminhtml_Mobile_Preview_Content extends Mage_Adminhtml_Block_Template
{
    /**
     * Configuration tab items array
     *
     * @var array
     */
    protected $tabItems = array();

    /**
     * Category item tint color styles
     *
     * @var string
     */
    protected $categoryItemTintColor = '';

    /**
     * Set path to template used for generating block's output.
     *
     * @param string $templateType
     * @return Mage_XmlConnect_Block_Adminhtml_Mobile_Preview_Content
     */
    public function setTemplate($templateType)
    {
        $deviceType = Mage::helper('xmlconnect')->getApplication()->getType();

        if ($deviceType == Mage_XmlConnect_Helper_Data::DEVICE_TYPE_IPHONE) {
            parent::setTemplate('xmlconnect/edit/tab/design/preview/' . $templateType . '.phtml');
        } else {
            parent::setTemplate('xmlconnect/edit/tab/design/preview/' . $templateType . '_' . $deviceType . '.phtml');
        }
        return $this;
    }

    /**
     * Prepare config data
     * Implement set "conf" data as magic method
     * Set config to 'tab_items' child block if exists
     *
     * @param array $conf
     */
    public function setConf($conf)
    {
        if (!is_array($conf)) {
            $conf = array();
        }
        $tabs = isset($conf['tabBar']) && isset($conf['tabBar']['tabs']) ? $conf['tabBar']['tabs'] : false;
        if ($tabs !== false) {
            foreach ($tabs->getEnabledTabs() as $tab) {
                $tab = (array) $tab;
                $conf['tabBar'][$tab['action']]['label'] = $tab['label'];
                $conf['tabBar'][$tab['action']]['image'] =
                    Mage::helper('xmlconnect/image')->getSkinImagesUrl('mobile_preview/' . $tab['image']);
            }
        }
        $tabItemsBlock = $this->getChild('tab_items');
        if ($tabItemsBlock !== false) {
            $tabItemsBlock->setData('conf', $conf);
        }
        $this->setData('conf', $conf);
    }

   /**
    * Get preview images url
    *
    * @param string $name - file name
    * @return string
    */
    public function getPreviewImagesUrl($name = '')
    {
        return  Mage::helper('xmlconnect/image')->getSkinImagesUrl('mobile_preview/' . $name);
    }


   /**
    * Retrieve url for images in the skin folder
    *
    * @param string $name - path to file name relative to the skin dir
    * @return string
    */
    public function getDesignPreviewImageUrl($name)
    {
        return Mage::helper('xmlconnect/image')->getSkinImagesUrl('design_default/' . $name);
    }

    /**
     * Get application banner image url
     *
     * @throws Mage_Core_Exception
     * @return string
     */
    public function getBannerImage()
    {
        $deviceType = Mage::helper('xmlconnect')->getApplication()->getType();
        switch ($deviceType) {
            case Mage_XmlConnect_Helper_Data::DEVICE_TYPE_IPHONE:

                if ($this->getData('conf/body/bannerImage')) {
                    $bannerImage = $this->getData('conf/body/bannerImage');
                } else {
                    $bannerImage = $this->getDesignPreviewImageUrl(
                        $this->getInterfaceImagesPaths('conf/body/bannerImage')
                    );
                }
                break;

            case Mage_XmlConnect_Helper_Data::DEVICE_TYPE_IPAD:

                $configPath = 'conf/body/bannerIpadImage';
                $imageUrlOrig = $this->getData($configPath);
                if ($imageUrlOrig) {
                    $width = Mage_XmlConnect_Helper_Ipad::PREVIEW_BANNER_WIDTH;
                    $height = Mage_XmlConnect_Helper_Ipad::PREVIEW_BANNER_HEIGHT;
                    $bannerImage = Mage::helper('xmlconnect/image')
                        ->getCustomSizeImageUrl($imageUrlOrig, $width, $height);
                } else {
                    $bannerImage = $this->getPreviewImagesUrl('ipad/banner_image.png');
                }
                break;

            case Mage_XmlConnect_Helper_Data::DEVICE_TYPE_ANDROID:

                $configPath = 'conf/body/bannerAndroidImage';
                if ($this->getData($configPath)) {
                    $bannerImage = $this->getData($configPath);
                } else {
                    $bannerImage = $this->getDesignPreviewImageUrl(
                        $this->getInterfaceImagesPaths($configPath)
                    );
                }
                break;

            default:
                Mage::throwException($this->__('Device doesn\'t recognized: "%s". Unable to load a helper.', $deviceType));
                break;
        }
        return $bannerImage;
    }

    /**
     * Get background image url according device type
     *
     * @param string $orientation type of orientation
     * @throws Mage_Core_Exception
     * @return string
     */
    public function getBackgroundImage($orientation = '')
    {
        $backgroundImage = '';
        $deviceType = Mage::helper('xmlconnect')->getApplication()->getType();
        switch ($deviceType) {
            case Mage_XmlConnect_Helper_Data::DEVICE_TYPE_IPHONE:
                $configPath = 'conf/body/backgroundImage';
                $imageUrlOrig = $this->getData($configPath);
                if ($imageUrlOrig) {
                    $backgroundImage = $imageUrlOrig;
                } else {
                    $backgroundImage = $this->getDesignPreviewImageUrl(
                        $this->getInterfaceImagesPaths($configPath)
                    );
                }
                break;

            case Mage_XmlConnect_Helper_Data::DEVICE_TYPE_IPAD:
                switch ($orientation) {
                    case Mage_XmlConnect_Helper_Ipad::ORIENTATION_LANDSCAPE:
                        $configPath = 'conf/body/backgroundIpadLandscapeImage';
                        $imageUrlOrig = $this->getData($configPath);
                        if ($imageUrlOrig) {
                            $width = Mage_XmlConnect_Helper_Ipad::PREVIEW_LANDSCAPE_BACKGROUND_WIDTH;
                            $height = Mage_XmlConnect_Helper_Ipad::PREVIEW_LANDSCAPE_BACKGROUND_HEIGHT;
                            $backgroundImage = Mage::helper('xmlconnect/image')
                                ->getCustomSizeImageUrl($imageUrlOrig, $width, $height);
                        } else {
                            $backgroundImage =
                                $this->getPreviewImagesUrl('ipad/background_home_landscape.jpg');
                        }
                    break;
                    case Mage_XmlConnect_Helper_Ipad::ORIENTATION_PORTRAIT:
                        $configPath = 'conf/body/backgroundIpadPortraitImage';
                        $imageUrlOrig = $this->getData($configPath);
                        if ($imageUrlOrig) {
                            $width = Mage_XmlConnect_Helper_Ipad::PREVIEW_PORTRAIT_BACKGROUND_WIDTH;
                            $height = Mage_XmlConnect_Helper_Ipad::PREVIEW_PORTRAIT_BACKGROUND_HEIGHT;
                            $backgroundImage = Mage::helper('xmlconnect/image')
                                ->getCustomSizeImageUrl($imageUrlOrig, $width, $height);
                        } else {
                            $backgroundImage =
                                $this->getPreviewImagesUrl('ipad/background_portrait.jpg');
                        }
                    break;
                    default:
                        Mage::throwException(
                            $this->__('Wrong Ipad background image orientation has been specified: "%s".', $param)
                        );
                }
                break;

            case Mage_XmlConnect_Helper_Data::DEVICE_TYPE_ANDROID:
                switch ($orientation) {
                    case Mage_XmlConnect_Helper_Android::ORIENTATION_LANDSCAPE:
                        $configPath = 'conf/body/backgroundAndroidLandscapeImage';
                        $imageUrlOrig = $this->getData($configPath);
                        if ($imageUrlOrig) {
                            $width = Mage_XmlConnect_Helper_Android::PREVIEW_LANDSCAPE_BACKGROUND_WIDTH;
                            $height = Mage_XmlConnect_Helper_Android::PREVIEW_LANDSCAPE_BACKGROUND_HEIGHT;
                            $backgroundImage = Mage::helper('xmlconnect/image')
                                ->getCustomSizeImageUrl($imageUrlOrig, $width, $height);
                        } else {
                            $backgroundImage =
                                $this->getPreviewImagesUrl('android/background_home_landscape.jpg');
                        }
                    break;
                    case Mage_XmlConnect_Helper_Android::ORIENTATION_PORTRAIT:
                        $configPath = 'conf/body/backgroundAndroidPortraitImage';
                        $imageUrlOrig = $this->getData($configPath);
                        if ($imageUrlOrig) {
                            $width = Mage_XmlConnect_Helper_Android::PREVIEW_PORTRAIT_BACKGROUND_WIDTH;
                            $height = Mage_XmlConnect_Helper_Android::PREVIEW_PORTRAIT_BACKGROUND_HEIGHT;
                            $backgroundImage = Mage::helper('xmlconnect/image')
                                ->getCustomSizeImageUrl($imageUrlOrig, $width, $height);
                        } else {
                            $backgroundImage =
                                $this->getPreviewImagesUrl('android/background_portrait.jpg');
                        }
                    break;
                    default:
                        Mage::throwException(
                            $this->__('Wrong Android background image orientation has been specified: "%s".', $orientation)
                        );
                }
                break;

            default:
                Mage::throwException(
                    $this->__('Device doesn\'t recognized: "%s". Unable to load a helper.', $deviceType)
                );
                break;
        }
        return $backgroundImage;

    }

    /**
     * Get font info from config
     *
     * @param string $path
     * @return string
     */
    public function getConfigFontInfo($path)
    {
        return $this->getData('conf/fonts/' . $path);
    }

    /**
     * Get icon logo url
     *
     * @return string
     */
    public function getLogoUrl()
    {
        $configPath = 'conf/navigationBar/icon';
        if ($this->getData($configPath)) {
            return $this->getData($configPath);
        } else {
            return $this->getDesignPreviewImageUrl($this->getInterfaceImagesPaths($configPath));
        }
    }

    /**
     * Converts Data path(conf/submision/zzzz) to config path (conf/native/submission/zzzzz)
     *
     * @param string $configPath
     * @return string
     */
    protected function _replaceConfig($configPath)
    {
        return $configPath = preg_replace('/^conf\/(.*)$/', 'conf/native/${1}', $configPath);
    }

    /**
     * Expose function getInterfaceImagesPaths from xmlconnect/images
     * Converts Data path(conf/submision/zzzz) to config path (conf/native/submission/zzzzz)
     *
     * @param string $path
     * @return array
     */
    public function getInterfaceImagesPaths($path)
    {
        $path = $this->_replaceConfig($path);
        return Mage::helper('xmlconnect/image')->getInterfaceImagesPaths($path);
    }

   /**
    * Get xmlconnect css url
    *
    * @param string $name - file name
    * @return string
    */
    public function getPreviewCssUrl($name = '')
    {
        return  Mage::getDesign()->getSkinUrl('xmlconnect/' . $name);
    }

    /**
     * Get category item tint color styles
     *
     * @return string
     */
    public function getCategoryItemTintColor()
    {
        if (!strlen($this->categoryItemTintColor)) {
            $percent = .4;
            $mask = 255;

            $hex = str_replace('#','',$this->getData('conf/categoryItem/tintColor'));
            $hex2 = '';
            $_rgb = array();

            $d = '[a-fA-F0-9]';

            if (preg_match("/^($d$d)($d$d)($d$d)\$/", $hex, $rgb)) {
                $_rgb = array(hexdec($rgb[1]), hexdec($rgb[2]), hexdec($rgb[3]));
            }
            if (preg_match("/^($d)($d)($d)$/", $hex, $rgb)) {
                $_rgb = array(hexdec($rgb[1] . $rgb[1]), hexdec($rgb[2] . $rgb[2]), hexdec($rgb[3] . $rgb[3]));
            }

            for ($i=0; $i<3; $i++) {
                $_rgb[$i] = round($_rgb[$i] * $percent) + round($mask * (1-$percent));
                if ($_rgb[$i] > 255) {
                    $_rgb[$i] = 255;
                }
            }

            for($i=0; $i < 3; $i++) {
                $hex_digit = dechex($_rgb[$i]);
                if(strlen($hex_digit) == 1) {
                    $hex_digit = "0" . $hex_digit;
                }
                $hex2 .= $hex_digit;
            }
            if($hex && $hex2){
                // for IE
                $this->categoryItemTintColor .= "filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#".$hex2."', endColorstr='#".$hex."');";
                // for webkit browsers
                $this->categoryItemTintColor .= "background:-webkit-gradient(linear, left top, left bottom, from(#".$hex2."), to(#".$hex."));";
                // for firefox
                $this->categoryItemTintColor .= "background:-moz-linear-gradient(top,  #".$hex2.",  #".$hex.");";
            }
        }
        return $this->categoryItemTintColor;
    }
}
