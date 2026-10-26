<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * @package    Mage_Core
 */
class Mage_Core_Model_Message_Warning extends Mage_Core_Model_Message_Abstract
{
    /**
     * @param string $code
     */
    public function __construct($code)
    {
        parent::__construct(Mage_Core_Model_Message::WARNING, $code);
    }
}
