<?php
/**
 * Payment centinel session model
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Centinel
 */
class Mage_Centinel_Model_Session extends Mage_Core_Model_Session_Abstract
{
    public function __construct()
    {
        $this->init('centinel_validator');
    }
}
