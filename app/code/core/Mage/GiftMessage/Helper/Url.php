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
 * Gift Message url helper
 *
 * @category   Mage
 * @package    Mage_GiftMessage
 */
class Mage_GiftMessage_Helper_Url extends Mage_Core_Helper_Url
{
    protected $_moduleName = 'Mage_GiftMessage';

    /**
     * Retrieve gift message save url
     *
     * @param string $type
     * @param array $params
     * @return string
     */
    public function getEditUrl(Varien_Object $item, $type, $params = [])
    {
        if ($item->getGiftMessageId()) {
            $params = array_merge($params, ['message' => $item->getGiftMessageId(), 'item' => $item->getId(), 'type' => $type]);
            return $this->_getUrl('giftmessage/index/edit', $params);
        } else {
            $params = array_merge($params, ['item' => $item->getId(), 'type' => $type]);
            return $this->_getUrl('giftmessage/index/new', $params);
        }
    }

    /**
     * Retrieve gift message button block url
     *
     * @param int $itemId
     * @param string $type
     * @param array $params
     * @return string
     */
    public function getButtonUrl($itemId, $type, $params = [])
    {
        $params = array_merge($params, ['item' => $itemId, 'type' => $type]);
        return $this->_getUrl('giftmessage/index/button', $params);
    }

    /**
     * Retrieve gift message remove url
     *
     * @param int $itemId
     * @param string $type
     * @param array $params
     * @return string
     */
    public function getRemoveUrl($itemId, $type, $params = [])
    {
        $params = array_merge($params, ['item' => $itemId, 'type' => $type]);
        return $this->_getUrl('giftmessage/index/remove', $params);
    }

    /**
     * Retrieve gift message save url
     *
     * @param int $itemId
     * @param string $type
     * @param string $giftMessageId
     * @param array $params
     * @return string
     */
    public function getSaveUrl($itemId, $type, $giftMessageId = null, $params = [])
    {
        if (!is_null($giftMessageId)) {
            $params = array_merge($params, ['message' => $giftMessageId, 'item' => $itemId, 'type' => $type]);
            return $this->_getUrl('giftmessage/index/save', $params);
        } else {
            $params = array_merge($params, ['item' => $itemId, 'type' => $type]);
            return $this->_getUrl('giftmessage/index/save', $params);
        }
    }
}
