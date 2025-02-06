<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Core
 */

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Core
 */
class Mage_Core_Model_Message_Error extends Mage_Core_Model_Message_Abstract
{
    /**
     * @param string $code
     */
    public function __construct($code)
    {
        parent::__construct(Mage_Core_Model_Message::ERROR, $code);
    }
}
