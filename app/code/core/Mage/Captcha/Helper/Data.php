<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Captcha
 */

/**
 * Captcha image model
 *
 * @package    Mage_Captcha
 */
class Mage_Captcha_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Used for "name" attribute of captcha's input field
     */
    public const INPUT_NAME_FIELD_VALUE = 'captcha';

    /**
     * Always show captcha
     */
    public const MODE_ALWAYS     = 'always';

    /**
     * Show captcha only after certain number of unsuccessful attempts
     */
    public const MODE_AFTER_FAIL = 'after_fail';

    /**
     * Captcha fonts path
     */
    public const XML_PATH_CAPTCHA_FONTS = 'default/captcha/fonts';

    protected $_moduleName = 'Mage_Captcha';

    /**
     * List uses Models of Captcha
     * @var array
     */
    protected $_captcha = [];

    /**
     * @since 19.4.19 / 20.0.17
     */
    public function isEnabled(): bool
    {
        $path = Mage::app()->getStore()->isAdmin() ? 'admin/captcha/enable' : 'customer/captcha/enable';
        return Mage::getStoreConfigFlag($path);
    }

    /**
     * Get Captcha
     *
     * @param string $formId
     * @return Mage_Captcha_Model_Zend
     */
    public function getCaptcha($formId)
    {
        if (!array_key_exists($formId, $this->_captcha)) {
            $type = $this->getConfigNode('type');
            $this->_captcha[$formId] = Mage::getModel('captcha/' . $type, ['formId' => $formId]);
        }

        return $this->_captcha[$formId];
    }

    /**
     * Returns value of the node with respect to current area (frontend or backend)
     *
     * @param string $id The last part of XML_PATH_$area_CAPTCHA_ constant (case-insensitive)
     * @param Mage_Core_Model_Store $store
     * @return Mage_Core_Model_Config_Element
     */
    public function getConfigNode($id, $store = null)
    {
        $areaCode = Mage::app()->getStore($store)->isAdmin() ? 'admin' : 'customer';
        return Mage::getStoreConfig($areaCode . '/captcha/' . $id, $store);
    }

    /**
     * Get list of available fonts
     * Return format:
     * [['arial'] => ['label' => 'Arial', 'path' => '/www/magento/fonts/arial.ttf']]
     *
     * @return array
     */
    public function getFonts()
    {
        $node = Mage::getConfig()->getNode(self::XML_PATH_CAPTCHA_FONTS);
        $fonts = [];
        if ($node) {
            foreach ($node->children() as $fontName => $fontNode) {
                $fonts[$fontName] = [
                    'label' => (string) $fontNode->label,
                    'path' => Mage::getBaseDir('base') . DS . $fontNode->path,
                ];
            }
        }

        return $fonts;
    }

    /**
     * Get captcha image directory
     *
     * @param mixed $website
     * @return string
     */
    public function getImgDir($website = null)
    {
        $websiteCode = Mage::app()->getWebsite($website)->getCode();
        $captchaDir = Mage::getBaseDir('media') . DS . 'captcha' . DS . $websiteCode . DS;
        $io = new Varien_Io_File();
        $io->checkAndCreateFolder($captchaDir, 0755);
        return $captchaDir;
    }

    /**
     * Get captcha image base URL
     *
     * @param mixed $website
     * @return string
     */
    public function getImgUrl($website = null)
    {
        $websiteCode = Mage::app()->getWebsite($website)->getCode();
        return Mage::getBaseUrl('media') . 'captcha' . '/' . $websiteCode . '/';
    }
}
