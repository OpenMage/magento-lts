<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\GiftMessage\Test\Block\Message\Order;

use Magento\Mtf\Client\Locator;
use Magento\Mtf\Client\Element\SimpleElement;

/**
 * Gift message block for order on order view page.
 */
class View extends \Mage\Sales\Test\Block\Order\View
{
    /**
     * Gift message fields.
     *
     * @var array
     */
    protected $giftMessageFields = [
        'sender' => './/dt[1]',
        'recipient' => './/dt[2]',
        'message' => './/dd'
    ];

    /**
     * Get gift message.
     *
     * @param SimpleElement|null $giftMessageElement
     * @return array
     */
    public function getGiftMessage(SimpleElement $giftMessageElement = null)
    {
        $message = [];
        $element = ($giftMessageElement !== null) ? $giftMessageElement : $this->_rootElement;
        foreach ($this->giftMessageFields as $key => $field) {
            $value = $element->find($field, Locator::SELECTOR_XPATH)->getText();
            $value = preg_match('`\w+: (.*)`', $value, $matches) ? $matches[1] : $value;
            $message[$key] = $value;
        }

        return $message;
    }
}
