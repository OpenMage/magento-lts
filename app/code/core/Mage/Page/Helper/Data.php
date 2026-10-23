<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Page
 */

/**
 * @package    Mage_Page
 */
class Mage_Page_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_moduleName = 'Mage_Page';

    public const XML_PATH_LOGO_SRC = 'design/header/logo_src';

    public const XML_PATH_LOGO_SRC_SMALL = 'design/header/logo_src_small';

    public const XML_PATH_LOGO_SRC_SMALL_SAME_AS_MAIN = 'design/header/logo_src_small_same_as_main';

    public const LOGO_MEDIA_DIR = 'header/logo/';

    public function getLogoSrc(string $src, ?string $storeCode = null): string
    {
        // Logo files uploaded in admin panel are stored in the media directory
        if (file_exists(Mage::getBaseDir('media') . DS . self::LOGO_MEDIA_DIR . $src)) {
            $mediaBaseUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
            return $mediaBaseUrl . self::LOGO_MEDIA_DIR . $src;
        }

        // Before upload feature was implemented, logo files are stored in the skin directory
        return $storeCode
            ? Mage::getDesign()->getSkinUrl($src, [
                '_area' => 'frontend',
                '_store' => Mage::app()->getStore($storeCode),
                '_package' => Mage::getStoreConfig('design/package/name', $storeCode),
            ])
            : Mage::getDesign()->getSkinUrl($src);
    }
}
