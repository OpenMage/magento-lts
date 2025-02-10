<?php
/**
 * Adminhtml backend model for "Custom Admin URL" option
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Backend_Admin_Custom extends Mage_Core_Model_Config_Data
{
    public const CONFIG_SCOPE                      = 'stores';
    public const CONFIG_SCOPE_ID                   = 0;

    public const XML_PATH_UNSECURE_BASE_URL        = 'web/unsecure/base_url';
    public const XML_PATH_SECURE_BASE_URL          = 'web/secure/base_url';
    public const XML_PATH_UNSECURE_BASE_LINK_URL   = 'web/unsecure/base_link_url';
    public const XML_PATH_SECURE_BASE_LINK_URL     = 'web/secure/base_link_url';

    /**
     * Validate value before save
     *
     * @return $this
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();

        if (!empty($value) && substr($value, -2) !== '}}') {
            $value = rtrim($value, '/') . '/';
        }

        $this->setValue($value);
        return $this;
    }

    /**
     * Change secure/unsecure base_url after use_custom_url was modified
     *
     * @return $this
     */
    public function _afterSave()
    {
        $useCustomUrl = $this->getData('groups/url/fields/use_custom/value');
        $value = $this->getValue();

        if ($useCustomUrl == 1 && empty($value)) {
            return $this;
        }

        if ($useCustomUrl == 1) {
            Mage::getConfig()->saveConfig(
                self::XML_PATH_SECURE_BASE_URL,
                $value,
                self::CONFIG_SCOPE,
                self::CONFIG_SCOPE_ID,
            );
            Mage::getConfig()->saveConfig(
                self::XML_PATH_UNSECURE_BASE_URL,
                $value,
                self::CONFIG_SCOPE,
                self::CONFIG_SCOPE_ID,
            );
        }

        return $this;
    }
}
