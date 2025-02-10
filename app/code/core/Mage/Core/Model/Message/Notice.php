<?php
/**
 * This file is part of OpenMage.
For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Core
 */
class Mage_Core_Model_Message_Notice extends Mage_Core_Model_Message_Abstract
{
    /**
     * Mage_Core_Model_Message_Notice constructor.
     * @param string $code
     */
    public function __construct($code)
    {
        parent::__construct(Mage_Core_Model_Message::NOTICE, $code);
    }
}
