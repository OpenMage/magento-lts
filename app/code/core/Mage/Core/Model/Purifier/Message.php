<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

declare(strict_types=1);

/**
 * A purifier model for {@link Mage_Core_Model_Message} text.
 *
 * Get an instance by calling `Mage::getSingleton('core/purifier_message');`
 *
 * @see Mage_Core_Model_Purifier_Interface
 * @see Mage_Core_Model_Purifier_Abstract
 * @package Mage_Core
 */
class Mage_Core_Model_Purifier_Message extends Mage_Core_Model_Purifier_Abstract implements Mage_Core_Model_Purifier_Interface
{
    public function __construct()
    {
        parent::__construct([
            self::OPTION_ALLOWED_ELEMENTS => [
                'a',
                'abbr',
                'b',
                'cite',
                'code',
                'em',
                'i',
                'q',
                's',
                'small',
                'span',
                'strong',
                'sub',
                'sup',
                'u',
            ],
            self::OPTION_ALLOWED_CLASSES => [],
            self::OPTION_ALLOWED_STYLE_PROPERTIES => [],
            self::OPTION_ESCAPE_INVALID_TAGS => true,
        ]);
    }
}
