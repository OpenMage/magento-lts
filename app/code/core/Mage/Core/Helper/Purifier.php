<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * A Helper for purifying HTML strings.
 *
 * "Purify" in this context means to sanitize HTML in order to prevent
 * vulnerabilities and/or broken HTML. It is also a reference to the
 * HTMLPurifier library that is used as a dependency in the original
 * implementation.
 *
 * @package Mage_Core
 * @see Mage_Core_Model_Purifier_Interface
 * @see Mage_Core_Model_Purifier
 */
class Mage_Core_Helper_Purifier extends Mage_Core_Helper_Abstract
{
    /**
     * @var string
     */
    #[Deprecated(message: <<<'TXT'
    Don't create your own {@link \HTMLPurifier}. Use
                 {@link \Mage_Core_Model_Purifier} via
                 `Mage::getModel('core/purifier', $options);`
    TXT)]
    public const CACHE_DEFINITION = 'Cache.DefinitionImpl';

    /**
     * @deprecated No longer used. See {@link static::$defaultPurifier}.
     * @see        static::$defaultPurifier
     */
    protected ?HTMLPurifier $purifier;

    protected Mage_Core_Model_Purifier_Interface $defaultPurifier;

    /**
     * Purifier Constructor Call
     *
     * @param null|HTMLPurifier $purifier **Deprecated:** Unused
     */
    public function __construct(?HTMLPurifier $purifier = null)
    {
        $this->purifier = $purifier;
        /** @var Mage_Core_Model_Purifier $defaultPurifier */
        $defaultPurifier = Mage::getModel('core/purifier');
        $this->defaultPurifier = $defaultPurifier;
    }

    /**
     * Purify Html Content
     *
     * @template T of string[]|string
     * @param  T $content
     * @return T
     */
    public function purify($content)
    {
        if (is_array($content)) {
            $purified = [];

            foreach ($content as $html) {
                $purified[] = $this->defaultPurifier->purify($html);
            }

            return $purified;
        }

        return $this->defaultPurifier->purify($content);
    }
}
