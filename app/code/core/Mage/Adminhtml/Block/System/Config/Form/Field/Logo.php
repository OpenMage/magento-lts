<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Image config field renderer
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_System_Config_Form_Field_Logo extends Varien_Data_Form_Element_Image
{
    protected ?string $url = null;

    /**
     * Get logo image preview url
     * @throws Mage_Core_Exception
     */
    protected function _getUrl(): string
    {
        if (is_null($this->url)) {
            /** @var Varien_Simplexml_Element $config */
            $config = $this->getFieldConfig();
            $path = $config->getName() === 'logo_src_small'
                ? Mage_Page_Helper_Data::XML_PATH_LOGO_SRC_SMALL
                : Mage_Page_Helper_Data::XML_PATH_LOGO_SRC;

            // Get the current scope of the admin
            $request = Mage::app()->getRequest();
            $storeCode = $request->getParam('store');
            $websiteCode = $request->getParam('website');

            // Use built-in config inheritance to get the logo src
            if ($storeCode) {
                $value = Mage::getStoreConfig($path, $storeCode);
            } elseif ($websiteCode) {
                $website = Mage::app()->getWebsite($websiteCode);
                $value = (string) $website->getConfig($path);
                $storeCode = null;
            } else {
                $value = (string) Mage::getConfig()->getNode('default/' . $path);
                $storeCode = null;
            }

            $this->url = $value ? Mage::helper('page')->getLogoSrc($value, $storeCode) : '';
        }

        return $this->url;
    }

    /**
     * Allow deletion of logo file in media directory only
     *
     * @throws Mage_Core_Exception
     */
    protected function _getDeleteCheckbox(): string
    {
        return str_contains($this->_getUrl(), '/media/')
            ? parent::_getDeleteCheckbox()
            : '';
    }
}
