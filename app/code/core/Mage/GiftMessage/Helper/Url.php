<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_GiftMessage
 */

/**
 * Gift Message url helper
 *
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
