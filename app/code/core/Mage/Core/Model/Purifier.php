<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

declare(strict_types=1);

/**
 * A model for purifying HTML strings.
 *
 * "Purify" in this context means to sanitize HTML in order to prevent
 * vulnerabilities and/or broken HTML. It is also a reference to the
 * HTMLPurifier library that is used as a dependency in the original
 * implementation.
 *
 * @package Mage_Core
 */
class Mage_Core_Model_Purifier
{
    protected const CONFIG_CACHE_DEFINITION_IMPL = 'Cache.DefinitionImpl';

    protected const CONFIG_CORE_ESCAPE_INVALID_TAGS = 'Core.EscapeInvalidTags';

    protected const CONFIG_HTML_ALLOWED_ELEMENTS = 'HTML.AllowedElements';

    /** @var \HTMLPurifier */
    protected $purifier;

    /**
     * Mage model constructor.
     *
     * NOTE: If inheriting from this class, note that the
     * {@link HTMLPurifier_Config} instance is finalized upon first *read*
     * operation on it, which often occurs on the first call of
     * {@link HTMLPurifier::purify()} or {@link HTMLPurifier::purifyArray()}.
     * Therefore, more configuration *can* happen after construction via
     * `$this->purifier->config->set('key', 'value');` calls, though this should
     * not be considered to be guaranteed behavior or part of the official API
     * of the class.
     *
     * The {@link $options}:
     * | Key              | Description                                                                                            | Default |
     * |------------------|--------------------------------------------------------------------------------------------------------|---------|
     * | allowedElements  | `null` to allow all valid HTML elements, or an array of allowed element names, e.g., `['b', 'i', 'u']` | `null`  |
     * | escapeInvalidTags| `true` to include invalid and forbidden tags as literal text, `false` to strip the tag                 | `false` |
     *
     * @param array{
     *     allowedElements?: string[],
     *     escapeInvalidTags?: bool,
     * } $options
     */
    public function __construct($options = [])
    {
        $allowedElements = $options['allowedElements'] ?? null;
        $escapeInvalidTags = $options['escapeInvalidTags'] ?? false;

        /** @var \Mage_Core_Helper_Purifier_Config $configHelper */
        $configHelper = Mage::helper('core/purifier_config');

        $config = HTMLPurifier_Config::createDefault();
        // Allow <a> target attribute (when <a> is allowed)
        $config->set('Attr.AllowedFrameTargets', ['_self', '_blank', '_parent', '_top']);
        // Allow <a> rel attribute (when <a> is allowed)
        $config->set('Attr.AllowedRel', ['nofollow', 'noopener', 'noreferrer']);
        // Don't "fix" deprecated HTML
        $config->set('HTML.TidyLevel', 'none');
        // Use custom cache for HTMLPurifier definitions
        $config->set(static::CONFIG_CACHE_DEFINITION_IMPL, $configHelper->getCacheDefinitionImpl());
        // Optionally set an allowlist for HTML elements
        $config->set(static::CONFIG_HTML_ALLOWED_ELEMENTS, $allowedElements);
        // Write invalid tags as escaped text instead of removing the content
        $config->set(static::CONFIG_CORE_ESCAPE_INVALID_TAGS, $escapeInvalidTags);

        $this->purifier = new HTMLPurifier($config);
    }

    /**
     * @return null|string[]
     */
    public function getAllowedElements()
    {
        return $this->purifier->config->get(static::CONFIG_HTML_ALLOWED_ELEMENTS);
    }

    /**
     * @return bool
     */
    public function getEscapeInvalidTags()
    {
        return $this->purifier->config->get(static::CONFIG_CORE_ESCAPE_INVALID_TAGS);
    }

    /**
     * Purify Html Content
     *
     * @param  string $html
     * @return string
     */
    public function purify($html)
    {
        if (!str_contains($html, '<')) {
            $decoded = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            return htmlspecialchars($decoded, ENT_NOQUOTES | ENT_SUBSTITUTE, 'UTF-8');
        }

        return $this->purifier->purify($html);
    }
}
