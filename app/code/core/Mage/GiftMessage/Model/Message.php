<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_GiftMessage
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Gift Message model
 *
 * @category   Mage
 * @package    Mage_GiftMessage
 *
 * @method Mage_GiftMessage_Model_Resource_Message _getResource()
 * @method Mage_GiftMessage_Model_Resource_Message getResource()
 * @method Mage_GiftMessage_Model_Resource_Message_Collection getCollection()
 *
 * @method int getCustomerId()
 * @method $this setCustomerId(int $value)
 * @method string getSender()
 * @method $this setSender(string $value)
 * @method string getRecipient()
 * @method $this setRecipient(string $value)
 * @method string getMessage()
 * @method $this setMessage(string $value)
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

    protected function _construct()
    {
        $this->_init('giftmessage/message');
    }

    /**
     * Return model from entity type
     *
     * @param string $type
     * @return Mage_Eav_Model_Entity_Abstract
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
