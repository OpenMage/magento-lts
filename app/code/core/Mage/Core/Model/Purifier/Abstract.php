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
    /**
     * Options key.
     *
     * *Value Type:* `null|string[]`
     *
     * *Default Value:* `null`
     *
     * *Description:* Provides a list of HTML attributes. Any attribute not
     *                present in the list will be stripped from the purified
     *                HTML. If unset or `null`, almost all standard attributes
     *                are allowed except for those considered to be "unsafe",
     *                such anything that allows JavaScript execution (e.g.,
     *                `onclick`) as well as the `id` attribute. The syntax for
     *                entries in this list is `'tag.attr'`, where `'*.attr'` is
     *                used for global attributes. The presence of an attribute
     *                in this list does not imply that its *values* will not be
     *                modified or removed. Note that `'*.id'` is never allowed,
     *                even when included in this list.
     */
    protected const OPTION_ALLOWED_ATTRIBUTES = 'allowedAttributes';

    /**
     * Options key.
     *
     * *Value Type:* `null|string[]`
     *
     * *Default Value:* `null`
     *
     * *Description:* Provides a list of CSS classes to allow as values for the
     *                global `class` HTML attribute. Any class not present in
     *                the list will be stripped from the purified HTML. If
     *                unset or `null`, all classes are allowed. The entries in
     *                this list are individual, case-sensitive, CSS class names.
     *                Note that this option has no effect if the `class`
     *                attribute is forbidden (see
     *                {@link static::OPTION_ALLOWED_ATTRIBUTES}).
     *
     * @see static::OPTION_ALLOWED_ATTRIBUTES
     */
    protected const OPTION_ALLOWED_CLASSES = 'allowedClasses';

    /**
     * Options key.
     *
     * *Value Type:* `null|string[]`
     *
     * *Default Value:* `null`
     *
     * *Description:* Provides a list of HTML elements. Any element not present
     *                in the list will be either stripped from the purified
     *                HTML, or rendered as literal text content, depending on
     *                the value of the
     *                {@link static::OPTION_ESCAPE_INVALID_TAGS} option. If
     *                unset or `null`, a liberal default list is used that
     *                includes all standard HTML elements except for those
     *                considered to be "unsafe" from malicious input (e.g.,
     *                `<script>`). The entries in this list are HTML element
     *                names with no extra syntax (e.g., no '<', '>'
     *                characters). Note that the aforementioned "unsafe"
     *                elements are never allowed, even when included in this
     *                list.
     *
     * @see static::OPTION_ESCAPE_INVALID_TAGS
     */
    protected const OPTION_ALLOWED_ELEMENTS = 'allowedElements';

    /**
     * Options key.
     *
     * *Value Type:* `null|string[]`
     *
     * *Default Value:* `null`
     *
     * *Description:* Provides a list of CSS properties to allow as values for
     *                the global `style` HTML attribute. Any CSS property not
     *                present in the list will be stripped from the purified
     *                HTML. If unset or `null`, a default list is used that
     *                includes all standard CSS properties except for those
     *                considered to be "unsafe" from CSS injection. The
     *                entries in this list are individual, case-sensitive, CSS
     *                property names. Note that the aforementioned "unsafe"
     *                properties are never allowed, even when included in this
     *                list. Note that this option has no effect if the
     *                `style` attribute is forbidden (see
     *                {@link static::OPTION_ALLOWED_ATTRIBUTES}). Note that
     *                this option *only* applies to inline `style` attributes
     *                in the HTML text and not to any styling done by
     *                stylesheets.
     *
     * @see static::OPTION_ALLOWED_ATTRIBUTES
     */
    protected const OPTION_ALLOWED_STYLE_PROPERTIES = 'allowedStyleProperties';

    /**
     * Options key.
     *
     * *Value Type:* `bool`
     *
     * *Default Value:* `false`
     *
     * *Description:* Determines whether forbidden elements (see
     *                {@link static::OPTION_ALLOWED_ELEMENTS}) are removed
     *                from the purified output or included as literal text
     *                content. This has no affect on parts of the input that
     *                are already written as text content.
     *
     * @see static::OPTION_ALLOWED_ELEMENTS
     */
    protected const OPTION_ESCAPE_INVALID_TAGS = 'escapeInvalidTags';

    private const string CONFIG_ATTR_ALLOWED_CLASSES = 'Attr.AllowedClasses';

    private const string CONFIG_CACHE_DEFINITION_IMPL = 'Cache.DefinitionImpl';

    private const string CONFIG_CORE_ESCAPE_INVALID_TAGS = 'Core.EscapeInvalidTags';

    private const string CONFIG_CSS_ALLOWED_PROPERTIES = 'CSS.AllowedProperties';

    private const string CONFIG_HTML_ALLOWED_ELEMENTS = 'HTML.AllowedElements';

    private const string CONFIG_HTML_ALLOWED_ATTRIBUTES = 'HTML.AllowedAttributes';

    private readonly HTMLPurifier $purifier;

    /**
     * Constructor.
     *
     * For detailed descriptions of the options and their effects, see:
     * - {@link self::OPTION_ALLOWED_ELEMENTS}
     * - {@link self::OPTION_ALLOWED_ATTRIBUTES}
     * - {@link self::OPTION_ALLOWED_CLASSES}
     * - {@link self::OPTION_ALLOWED_STYLE_PROPERTIES}
     * - {@link self::OPTION_ESCAPE_INVALID_TAGS}
     *
     * Notes and Suggestions:
     * - The defaults should be fine for most use cases that just want to
     * sanitize arbitrary HTML. **However**, do note that the defaults allow
     * for a lot of freedom in styling. Supplied input may set any `class` and
     * may set `style` attributes that mess up intended layout/formatting by,
     * e.g., setting strange margins or font sizes.
     * - Be careful when customizing `allowedAttributes`. It's easy to forget
     * legitimate attributes like `'a.href'`, without which `<a>` tags are
     * essentially useless. It's also not usually necessary (see below). It
     * may occasionally be desirable to set it to `[]` for very restrictive
     * contexts, but again, one must make sure to not accidentally break the
     * functionality of the `allowedElements`.
     * - A simple way to disable all styling without having to craft a custom
     * `allowedAttributes` list is to simply set `allowedClasses` and
     * `allowedStyleProperties` to `[]`. This will strip all values from the
     * `class` and `style` attributes, and since the purifier already strips
     * empty `class` and `style` attributes from the output, this results in
     * all `class` and `style` attributes being removed.
     *
     * **WARNING:**
     * All options that expect a list of strings (`string[]`) are expected to
     * have only numeric keys, starting from 0 with no gaps ("dense"). Other
     * shapes may cause unexpected results.
     *
     * @param array{
     *     allowedElements?: null|string[],
     *     allowedAttributes?: null|string[],
     *     allowedClasses?: null|string[],
     *     allowedStyleProperties?: null|string[],
     *     escapeInvalidTags?: bool,
     * } $options
     */
    public function __construct(array $options = [])
    {
        $allowedElements = $options[static::OPTION_ALLOWED_ELEMENTS] ?? null;
        $allowedAttributes = $options[static::OPTION_ALLOWED_ATTRIBUTES] ?? null;
        $allowedClasses = $options[static::OPTION_ALLOWED_CLASSES] ?? null;
        $allowedStyleProperties = $options[static::OPTION_ALLOWED_STYLE_PROPERTIES] ?? null;
        $escapeInvalidTags = $options[static::OPTION_ESCAPE_INVALID_TAGS] ?? false;

        /** @var Mage_Core_Helper_Purifier_DefinitionCache $definitionCacheHelper */
        $definitionCacheHelper = Mage::helper('core/purifier_definitionCache');

        $config = HTMLPurifier_Config::createDefault();
        // Allow <a> target attribute (when <a> is allowed)
        $config->set('Attr.AllowedFrameTargets', ['_self', '_blank', '_parent', '_top']);
        // Allow <a> rel attribute (when <a> is allowed)
        $config->set('Attr.AllowedRel', ['nofollow', 'noopener', 'noreferrer']);
        // Don't "fix" deprecated HTML
        $config->set('HTML.TidyLevel', 'none');
        // Use custom cache for HTMLPurifier definitions
        $config->set(self::CONFIG_CACHE_DEFINITION_IMPL, $definitionCacheHelper->getCacheDefinitionImpl());
        // Optionally set an allowlist for HTML elements
        $config->set(self::CONFIG_HTML_ALLOWED_ELEMENTS, $allowedElements);
        // Optionally set an allowlist for HTML element attributes
        $config->set(self::CONFIG_HTML_ALLOWED_ATTRIBUTES, $allowedAttributes);
        // Optionally set an allowlist for class attributes
        $config->set(self::CONFIG_ATTR_ALLOWED_CLASSES, $allowedClasses);
        // Optionally set an allowlist for CSS properties allowed in style attributes
        $config->set(self::CONFIG_CSS_ALLOWED_PROPERTIES, $allowedStyleProperties);
        // Write invalid tags as escaped text instead of removing the content
        $config->set(self::CONFIG_CORE_ESCAPE_INVALID_TAGS, $escapeInvalidTags);

        $this->purifier = new HTMLPurifier($config);
    }

    /**
     * @return null|string[]
     */
    final public function getAllowedAttributes(): ?array
    {
        return $this->purifier->config->get(self::CONFIG_HTML_ALLOWED_ATTRIBUTES);
    }

    /**
     * @return null|string[]
     */
    final public function getAllowedElements(): ?array
    {
        return $this->purifier->config->get(self::CONFIG_HTML_ALLOWED_ELEMENTS);
    }

    /**
     * @return null|string[]
     */
    final public function getAllowedClasses(): ?array
    {
        return $this->purifier->config->get(self::CONFIG_ATTR_ALLOWED_CLASSES);
    }

    /**
     * @return null|string[]
     */
    final public function getAllowedStyleProperties(): ?array
    {
        return $this->purifier->config->get(self::CONFIG_CSS_ALLOWED_PROPERTIES);
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
