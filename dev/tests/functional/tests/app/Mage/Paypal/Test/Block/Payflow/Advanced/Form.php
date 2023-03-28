<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Paypal\Test\Block\Payflow\Advanced;

use Magento\Mtf\Client\Element\SimpleElement;

/**
 * Card Verification frame for PayPal advanced payment method.
 */
class Form extends \Magento\Mtf\Block\Form
{
    /**
     * 'Pay Now' button selector.
     *
     * @var string
     */
    protected $continue = '#btn_pay_cc';

    /**
     * Loader selector.
     *
     * @var string
     */
    protected $loader = '#lightBoxDiv';

    /**
     * Click "Pay Now" button.
     *
     * @param SimpleElement $element
     * @return void
     */
    public function clickPayNow(SimpleElement $element)
    {
        $element->find($this->continue)->click();
        $this->browser->selectWindow();
    }
}
