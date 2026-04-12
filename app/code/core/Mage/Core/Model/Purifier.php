<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

declare(strict_types=1);

/**
 * De facto model implementation of {@link Mage_Core_Model_Purifier_Interface}.
 *
 * NOTE: Instances of this model are somewhat heavy, so it's best to cache
 * instances that use identical configurations.
 *
 * @see Mage_Core_Model_Purifier_Interface
 * @see Mage_Core_Model_Purifier_Abstract
 * @package Mage_Core
 */
class Mage_Core_Model_Purifier extends Mage_Core_Model_Purifier_Abstract implements Mage_Core_Model_Purifier_Interface
{
    /**
     * @inheritDoc
     */
    final public const OPTION_ALLOWED_ATTRIBUTES = parent::OPTION_ALLOWED_ATTRIBUTES;

    /**
     * @inheritDoc
     */
    final public const OPTION_ALLOWED_CLASSES = parent::OPTION_ALLOWED_CLASSES;

    /**
     * @inheritDoc
     */
    final public const OPTION_ALLOWED_ELEMENTS = parent::OPTION_ALLOWED_ELEMENTS;

    /**
     * @inheritDoc
     */
    final public const OPTION_ALLOWED_STYLE_PROPERTIES = parent::OPTION_ALLOWED_STYLE_PROPERTIES;

    /**
     * @inheritDoc
     */
    final public const OPTION_ESCAPE_INVALID_TAGS = parent::OPTION_ESCAPE_INVALID_TAGS;
}
