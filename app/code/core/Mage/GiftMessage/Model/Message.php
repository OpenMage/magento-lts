<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_GiftMessage
 */

/**
 * Gift Message model
 *
 * @package    Mage_GiftMessage
 *
 * @method Mage_GiftMessage_Model_Resource_Message            _getResource()
 * @method Mage_GiftMessage_Model_Resource_Message_Collection getCollection()
 * @method int                                                getCustomerId()
 * @method string                                             getMessage()
 * @method string                                             getRecipient()
 * @method Mage_GiftMessage_Model_Resource_Message            getResource()
 * @method Mage_GiftMessage_Model_Resource_Message_Collection getResourceCollection()
 * @method string                                             getSender()
 * @method $this                                              setCustomerId(int $value)
 * @method $this                                              setMessage(string $value)
 * @method $this                                              setRecipient(string $value)
 * @method $this                                              setSender(string $value)
 */
class Mage_GiftMessage_Model_Message extends Mage_Core_Model_Abstract
{
    /**
     * Allowed types of entities for using of gift messages
     *
     * @var array
     */
    protected static $_allowedEntityTypes = [
        'order'         => 'sales/order',
        'order_item'    => 'sales/order_item',
        'order_address' => 'sales/order_address',
        'quote'         => 'sales/quote',
        'quote_item'    => 'sales/quote_item',
        'quote_address' => 'sales/quote_address',
        'quote_address_item' => 'sales/quote_address_item',
    ];

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('giftmessage/message');
    }

    /**
     * Return model from entity type
     *
     * @param  string                   $type
     * @return Mage_Core_Model_Abstract
     * @throws Mage_Core_Exception
     */
    public function getEntityModelByType($type)
    {
        $types = self::getAllowedEntityTypes();
        if (!isset($types[$type])) {
            Mage::throwException(Mage::helper('giftmessage')->__('Unknown entity type'));
        }

        return Mage::getModel($types[$type]);
    }

    /**
     * Checks that gift message is empty
     *
     * @return bool
     */
    public function isMessageEmpty()
    {
        return trim($this->getMessage()) == '';
    }

    /**
     * Return list of allowed entities for using in gift messages
     *
     * @return array
     */
    public static function getAllowedEntityTypes()
    {
        return self::$_allowedEntityTypes;
    }
}
