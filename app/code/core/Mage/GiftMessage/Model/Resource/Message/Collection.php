<?php
/**
 * Gift Message collection
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_GiftMessage
 */
class Mage_GiftMessage_Model_Resource_Message_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('giftmessage/message');
    }
}
