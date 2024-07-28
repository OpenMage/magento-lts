<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Tests
 * @package    Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
