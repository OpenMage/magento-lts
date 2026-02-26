<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

declare(strict_types=1);

/**
 * Default implementation of {@link Mage_Core_Model_Purifier_Interface}.
 *
 * This abstract class provides a standard, configurable, implementation of
 * {@link Mage_Core_Model_Purifier_Interface}. Derived classes can only
 * customize the purification behavior by passing options to the constructor.
 * If further customization is needed, consider using a different
 * implementation of {@link Mage_Core_Model_Purifier_Interface}.
 *
 * The implementation of this class contains no mutable state, but derived
 * classes may or may not be immutable.
 *
 * The de facto model class for this implementation is
 * {@link Mage_Core_Model_Purifier}. That can be used for ad-hoc purifier
 * instances. However, instances of this class can be somewhat heavy in memory
 * consumption and have an amortized cost of generating internal parsing rules
 * once per instance. It can be wise to create model subclasses with zero
 * argument constructors that always pass exactly the same options to the parent
 * constructor. Such a class is a good candidate for `Mage::getSingleton()` use.
 *
 * @see Mage_Core_Model_Purifier_Interface
 * @see Mage_Core_Model_Purifier
 * @package Mage_Core
 */
abstract class Mage_Core_Model_Purifier_Abstract implements Mage_Core_Model_Purifier_Interface
{
    private const CONFIG_CACHE_DEFINITION_IMPL = 'Cache.DefinitionImpl';

    private const CONFIG_CORE_ESCAPE_INVALID_TAGS = 'Core.EscapeInvalidTags';

    private const CONFIG_HTML_ALLOWED_ELEMENTS = 'HTML.AllowedElements';

    private readonly HTMLPurifier $purifier;

    /**
     * Mage model constructor.
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
    public function __construct(array $options = [])
    {
        $allowedElements = $options['allowedElements'] ?? null;
        $escapeInvalidTags = $options['escapeInvalidTags'] ?? false;

        /** @var Mage_Core_Helper_Purifier_Config $configHelper */
        $configHelper = Mage::helper('core/purifier_config');

        $config = HTMLPurifier_Config::createDefault();
        // Allow <a> target attribute (when <a> is allowed)
        $config->set('Attr.AllowedFrameTargets', ['_self', '_blank', '_parent', '_top']);
        // Allow <a> rel attribute (when <a> is allowed)
        $config->set('Attr.AllowedRel', ['nofollow', 'noopener', 'noreferrer']);
        // Don't "fix" deprecated HTML
        $config->set('HTML.TidyLevel', 'none');
        // Use custom cache for HTMLPurifier definitions
        $config->set(self::CONFIG_CACHE_DEFINITION_IMPL, $configHelper->getCacheDefinitionImpl());
        // Optionally set an allowlist for HTML elements
        $config->set(self::CONFIG_HTML_ALLOWED_ELEMENTS, $allowedElements);
        // Write invalid tags as escaped text instead of removing the content
        $config->set(self::CONFIG_CORE_ESCAPE_INVALID_TAGS, $escapeInvalidTags);

        $this->purifier = new HTMLPurifier($config);
    }

    /**
     * @return null|string[]
     */
    final public function getAllowedElements(): ?array
    {
        return $this->purifier->config->get(self::CONFIG_HTML_ALLOWED_ELEMENTS);
    }

    final public function getEscapeInvalidTags(): bool
    {
        return $this->purifier->config->get(self::CONFIG_CORE_ESCAPE_INVALID_TAGS);
    }

    /**
     * Purify Html Content
     */
    final public function purify(string $html): string
    {
        // As an optimization, we know that a string that has no '<' character
        // is text content, so we can avoid the overhead of HTMLPurifier. The
        // specific calls and flag parameters were chosen to exactly match the
        // output from HTMLPurifier being called with the same text, so that
        // the output is identical with or without the optimization.

        if (!str_contains($html, '<')) {
            $decoded = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            return htmlspecialchars($decoded, ENT_NOQUOTES | ENT_SUBSTITUTE, 'UTF-8');
        }

        return (string) $this->purifier->purify($html);
    }
}
