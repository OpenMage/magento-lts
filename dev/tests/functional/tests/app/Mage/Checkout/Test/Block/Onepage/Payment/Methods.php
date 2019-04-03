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
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Checkout\Test\Block\Onepage\Payment;

use Mage\Checkout\Test\Block\Onepage\AbstractOnepage;
use Mage\Payment\Test\Block\Form\Cc;

/**
 * One page checkout status payment method block.
 */
class Methods extends AbstractOnepage
{
    /**
     * Checkout loader selector.
     *
     * @var string
     */
    protected $waiterSelector = '#payment-please-wait';

    /**
     * Payment method input selector.
     *
     * @var string
     */
    protected $paymentMethodInput = '#p_method_%s';

    /**
     * Labels for payment methods.
     *
     * @var string
     */
    protected $paymentMethodLabels = '[for^=p_method_]';

    /**
     * Label for payment methods.
     *
     * @var string
     */
    protected $paymentMethodLabel = '[for=p_method_%s]';

    /**
     * Continue checkout button.
     *
     * @var string
     */
    protected $continue = '#payment-buttons-container button';

    /**
     * Purchase order number selector.
     *
     * @var string
     */
    protected $purchaseOrderNumber = '#po_number';

    /**
     * Select payment method.
     *
     * @param array $payment
     * @throws \Exception
     * @return void
     */
    public function selectPaymentMethod(array $payment)
    {
        $paymentMethod = $this->_rootElement->find(sprintf($this->paymentMethodInput, $payment['method']));
        if ($paymentMethod->isVisible()) {
            $paymentMethod->click();
        } else {
            $paymentCount = count($this->_rootElement->getElements($this->paymentMethodLabels));
            $paymentMethodLabel = $this->_rootElement->find(sprintf($this->paymentMethodLabel, $payment['method']));
            if ($paymentCount !== 1 && !$paymentMethodLabel->isVisible()) {
                throw new \Exception('Such payment method is absent.');
            }
        }
        if ($payment['method'] == "purchaseorder") {
            $this->_rootElement->find($this->purchaseOrderNumber)->setValue($payment['po_number']);
        }
        if (isset($payment['cc']) && !isset($payment['iframe'])) {
            $this->fillCreditCard($payment);
        }
    }

    /**
     * Fill credit card.
     *
     * @param array $payment
     * @return void
     */
    protected function fillCreditCard(array $payment)
    {
        /** @var Cc $formBlock */
        $formBlock = $this->blockFactory->create(
            'Mage\Payment\Test\Block\Form\Cc',
            ['element' => $this->_rootElement->find('#payment_form_' . $payment['method'])]
        );
        $formBlock->fill($payment['cc']);
    }
}
