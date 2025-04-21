<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

/**
 * Renderer for Payflow Link information
 *
 * @package    Mage_Paypal
 */
class Mage_Paypal_Block_Adminhtml_System_Config_Payflowlink_Info extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * Template path
     *
     * @var string
     */
    protected $_template = 'paypal/system/config/payflowlink/info.phtml';

    /**
     * Render fieldset html
     *
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $columns = ($this->getRequest()->getParam('website') || $this->getRequest()->getParam('store')) ? 5 : 4;
        return $this->_decorateRowHtml($element, "<td colspan='$columns'>" . $this->toHtml() . '</td>');
    }

    /**
     * Get frontend url
     *
     * @deprecated since 1.7.0.1
     * @param string $routePath
     * @return string
     */
    public function getFrontendUrl($routePath)
    {
        if ($this->getRequest()->getParam('website')) {
            $website = Mage::getModel('core/website')->load($this->getRequest()->getParam('website'));
            $secure = Mage::getStoreConfigFlag(
                Mage_Core_Model_Url::XML_PATH_SECURE_IN_FRONT,
                $website->getDefaultStore(),
            );
            $path = $secure ?
                Mage_Core_Model_Store::XML_PATH_SECURE_BASE_LINK_URL :
                Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_LINK_URL;
            $websiteUrl = Mage::getStoreConfig($path, $website->getDefaultStore());
        } else {
            $secure = Mage::getStoreConfigFlag(
                Mage_Core_Model_Url::XML_PATH_SECURE_IN_FRONT,
            );
            $websiteUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK, $secure);
        }

        return $websiteUrl . $routePath;
    }
}
