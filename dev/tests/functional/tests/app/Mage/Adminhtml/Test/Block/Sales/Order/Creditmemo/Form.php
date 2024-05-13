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

namespace Mage\Adminhtml\Test\Block\Sales\Order\Creditmemo;

use Mage\Adminhtml\Test\Block\Sales\Order\AbstractForm;

/**
 * Credit memo create form.
 */
class Form extends AbstractForm
{
    /**
     * Items block css selector.
     *
     * @var string
     */
    protected $items = '#creditmemo_item_container';

    /**
     * Item block class.
     *
     * @var string
     */
    protected $itemBlockClass = 'Mage\Adminhtml\Test\Block\Sales\Order\Creditmemo\Form\Items';

    /**
     * Online refund button.
     *
     * @var string
     */
    protected $onlineRefund = '[onclick*="submitCreditMemo()"]';

    /**
     * Offline refund button.
     *
     * @var string
     */
    protected $offlineRefund = '[onclick*="submitCreditMemoOffline()"]';

    /**
     * Fill items data.
     *
     * @param array $data
     * @param array $products
     * @return void
     */
    protected function fillItemsData(array $data, array $products)
    {
        parent::fillItemsData($data, $products);
        $this->updateQty();
    }

    /**
     * Online refund.
     *
     * @return void
     */
    public function onlineRefund()
    {
        $this->waitUntilRefundButtonVisible('online');
        $this->_rootElement->find($this->onlineRefund)->click();
    }

    /**
     * Offline refund.
     *
     * @return void
     */
    public function offlineRefund()
    {
        $this->waitUntilRefundButtonVisible('offline');
        $this->_rootElement->find($this->offlineRefund)->click();
    }

    /**
     * Wait until refund button is visible.
     *
     * @param string $refundType
     * @return void
     */
    protected function waitUntilRefundButtonVisible($refundType)
    {
        $this->getTemplateBlock()->waitLoader();
        $browser = $this->browser;
        $button = $refundType . "Refund";
        $selector = $this->$button . $this->disabledFlag;
        $browser->waitUntil(
            function () use ($browser, $selector) {
                $element = $browser->find($selector);
                return $element->isVisible() == false ? true : null;
            }
        );
    }
}
