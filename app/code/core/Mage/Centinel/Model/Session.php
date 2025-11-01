<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Centinel
 */

/**
 * Payment centinel session model
 *
 * @package    Mage_Centinel
 */
class Mage_Centinel_Model_Session extends Mage_Core_Model_Session_Abstract
{
    public function __construct()
    {
        $this->init('centinel_validator');
    }
}
