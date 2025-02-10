<?php
/**
 * Resource authorizenet debug model
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Paygate
 */
class Mage_Paygate_Model_Resource_Authorizenet_Debug extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('paygate/authorizenet_debug', 'debug_id');
    }
}
