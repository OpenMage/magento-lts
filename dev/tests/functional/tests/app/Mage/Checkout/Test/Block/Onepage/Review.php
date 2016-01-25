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
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Checkout\Test\Block\Onepage;

use Mage\Checkout\Test\Block\Onepage\Review\Items;
use Mage\Checkout\Test\Block\Onepage\Review\Totals;
use Mage\Paypal\Test\Block\Form\Centinel;
use Magento\Mtf\Client\Locator;
use Magento\Mtf\Fixture\InjectableFixture;
use Mage\Checkout\Test\Fixture\CheckoutAgreement;

/**
 * One page checkout status review block.
 */
class Review extends AbstractOnepage
{
    /**
     * Items block css selector.
     *
     * @var string
     */
    protected $items = 'tbody';

    /**
     * Place order checkout button.
     *
     * @var string
     */
    protected $continue = '#review-buttons-container button';

    /**
     * Css selector for total block.
     *
     * @var string
     */
    protected $total = 'tfoot';

    /**
     * Items block class.
     *
     * @var string
     */
    protected $itemsBlock = 'Mage\Checkout\Test\Block\Onepage\Review\Items';

    /**
     * Centinel form selector.
     *
     * @var string
     */
    protected $centinelForm = '#centinel_authenticate_block .authentication';

    /**
     * Iframe selector.
     *
     * @var string
     */
    protected $iFrameSelector = '#centinel_authenticate_iframe';

    /**
     * Body selector.
     *
     * @var string
     */
    protected $body = 'body';

    /**
     * Agreement locator.
     *
     * @var string
     */
    protected $agreement = './/div[contains(@id, "checkout-review-submit")]//label[.="%s"]';

    /**
     * Agreement checkbox locator.
     *
     * @var string
     */
    protected $agreementCheckbox = './/input[contains(@id, "agreement") and @title="%s"]';

    /**
     * Get items product block.
     *
     * @param string|null $productType
     * @return Items
     */
    public function getItemsBlock($productType = null)
    {
        return $this->hasRender($productType)
            ? $this->callRender($productType, 'getItemsBlock')
            : $this->blockFactory->create($this->itemsBlock, ['element' => $this->_rootElement->find($this->items)]);
    }

    /**
     * Get items product block.
     *
     * @return Totals
     */
    public function getTotalBlock()
    {
        return $this->blockFactory->create(
            'Mage\Checkout\Test\Block\Onepage\Review\Totals',
            ['element' => $this->_rootElement->find($this->total)]
        );
    }

    /**
     * Get 3D secure Form.
     *
     * @return Centinel
     */
    public function getCentinelForm()
    {
        return $this->blockFactory->create(
            'Mage\Paypal\Test\Block\Form\Centinel',
            ['element' => $this->_rootElement->find($this->centinelForm)]
        );
    }

    /**
     * Get verification response text.
     *
     * @return string
     */
    public function getVerificationResponseText()
    {
        $this->browser->switchToFrame(new Locator($this->iFrameSelector));
        $responseText = $this->browser->find($this->body)->getText();
        $this->browser->switchToFrame();
        return $responseText;
    }

    /**
     * Get alert massage.
     *
     * @return string
     */
    protected function getAlertMassage()
    {
        try {
            $alertText = $this->browser->getAlertText();
        } catch (\Exception $e) {
            $alertText = '';
        }
        return $alertText;
    }

    /**
     * Set agreement.
     *
     * @param CheckoutAgreement $agreement
     * @param string $value
     * @return void
     */
    public function setAgreement(CheckoutAgreement $agreement, $value)
    {
        $agreementsInputSelector = sprintf($this->agreementCheckbox, $agreement->getCheckboxText());
        $this->_rootElement->find($agreementsInputSelector, Locator::SELECTOR_XPATH, 'checkbox')->setValue($value);
    }

    /**
     * Check agreement.
     *
     * @param CheckoutAgreement $agreement
     * @return bool
     */
    public function checkAgreement(CheckoutAgreement $agreement)
    {
        return $this->_rootElement
            ->find(sprintf($this->agreement, $agreement->getCheckboxText()), Locator::SELECTOR_XPATH)
            ->isVisible();
    }

    /**
     * Click continue button.
     *
     * @return string
     */
    public function clickContinue()
    {
        $this->_rootElement->find($this->continue)->click();
        $alertText = $this->getAlertMassage();
        $this->waitLoader();

        return $alertText;
    }
}
