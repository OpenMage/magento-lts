<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml backend model for "Custom Admin URL" option
 *
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

    public const XML_PATH_UNSECURE_BASE_SKIN_URL   = 'web/unsecure/base_skin_url';

    public const XML_PATH_SECURE_BASE_SKIN_URL     = 'web/secure/base_skin_url';

    public const XML_PATH_UNSECURE_BASE_JS_URL     = 'web/unsecure/base_js_url';

    public const XML_PATH_SECURE_BASE_JS_URL       = 'web/secure/base_js_url';

    public const XML_PATH_COOKIE_DOMAIN            = 'web/cookie/cookie_domain';

    /**
     * Validate custom admin URL before save
     *
     * @return $this
     * @throws Mage_Core_Exception
     */
    #[Override]
    protected function _beforeSave()
    {
        $value = trim($this->getValue());

        // Empty is allowed (disabled custom admin URL)
        if (empty($value)) {
            $this->setValue($value);
            return $this;
        }

        // Whitelist: only allow valid base URL characters
        // Valid for base URL: letters, numbers, and URL-safe characters (: / . - _ [ ])
        if (!preg_match('/^[a-zA-Z0-9:\/.\-_\[\]]+$/', $value)) {
            Mage::throwException(
                Mage::helper('adminhtml')->__('Custom Admin URL contains invalid characters.'),
            );
        }

        // Parse the URL
        $urlParts = parse_url($value);

        if ($urlParts === false) {
            Mage::throwException(
                Mage::helper('adminhtml')->__('Invalid Custom Admin URL format.'),
            );
        }

        // Must have protocol
        if (!isset($urlParts['scheme'])) {
            Mage::throwException(
                Mage::helper('adminhtml')->__('Custom Admin URL must include protocol (http:// or https://).'),
            );
        }

        // Only allow http and https
        if (!in_array($urlParts['scheme'], ['http', 'https'], true)) {
            Mage::throwException(
                Mage::helper('adminhtml')->__('Custom Admin URL must use http:// or https:// protocol.'),
            );
        }

        // Must have hostname
        if (!isset($urlParts['host']) || empty($urlParts['host'])) {
            Mage::throwException(
                Mage::helper('adminhtml')->__('Custom Admin URL must include a hostname.'),
            );
        }

        // Ensure trailing slash
        if (!str_ends_with($value, '/')) {
            $value .= '/';
        }

        $this->setValue($value);

        // Set redirect flag if custom admin URL changed
        if ($this->getOldValue() != $value) {
            Mage::register('custom_admin_path_redirect', true, true);
        }

        return $this;
    }

    /**
     * Change secure/unsecure base_url, base_skin_url, base_js_url and cookie_domain
     * (admin store scope only) after use_custom_url was modified.
     *
     * Static assets (skin/js) and the session cookie are not covered by
     * base_url/base_link_url alone: Mage_Core_Model_Store::getBaseUrl() reads
     * base_skin_url/base_js_url as their own, independent config paths (see
     * app/code/core/Mage/Core/etc/config.xml), and Mage_Core_Model_Cookie::getDomain()
     * uses cookie_domain verbatim if it's non-empty. Without this, a custom
     * admin URL on a separate (sub)domain loads skin/js assets from the
     * storefront's base_url/base_skin_url instead (typically blocked there by
     * a hardened webserver config), and the session cookie's Domain attribute
     * ends up scoped to the storefront domain instead of the admin one,
     * breaking session/form-key continuity in the admin panel. cookie_domain
     * is pinned to the custom admin URL's own host explicitly (not left
     * empty) so the Domain attribute never depends on the current request's
     * Host header.
     *
     * @return $this
     */
    #[Override]
    protected function _afterSave()
    {
        $useCustomUrl = $this->getData('groups/url/fields/use_custom/value');
        $value = $this->getValue();

        // If use_custom is disabled OR value is empty, just save the value (don't update base URLs)
        if ($useCustomUrl != 1 || empty($value)) {
            return $this;
        }

        // If use_custom is enabled AND value is not empty, update base URLs
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

        // Static assets (skin/js) are separate config paths, not derived from base_url
        Mage::getConfig()->saveConfig(
            self::XML_PATH_SECURE_BASE_SKIN_URL,
            $value . 'skin/',
            self::CONFIG_SCOPE,
            self::CONFIG_SCOPE_ID,
        );
        Mage::getConfig()->saveConfig(
            self::XML_PATH_UNSECURE_BASE_SKIN_URL,
            $value . 'skin/',
            self::CONFIG_SCOPE,
            self::CONFIG_SCOPE_ID,
        );
        Mage::getConfig()->saveConfig(
            self::XML_PATH_SECURE_BASE_JS_URL,
            $value . 'js/',
            self::CONFIG_SCOPE,
            self::CONFIG_SCOPE_ID,
        );
        Mage::getConfig()->saveConfig(
            self::XML_PATH_UNSECURE_BASE_JS_URL,
            $value . 'js/',
            self::CONFIG_SCOPE,
            self::CONFIG_SCOPE_ID,
        );

        // Pin the admin store's cookie domain explicitly to the custom admin
        // URL's own host, rather than leaving it empty — an empty value
        // would make Mage_Core_Model_Cookie::getDomain() derive the Domain
        // attribute from the current request's Host header instead, which
        // is client-supplied input and therefore a weaker choice than a
        // known, fixed value.
        Mage::getConfig()->saveConfig(
            self::XML_PATH_COOKIE_DOMAIN,
            (string) parse_url($value, PHP_URL_HOST),
            self::CONFIG_SCOPE,
            self::CONFIG_SCOPE_ID,
        );

        return $this;
    }
}
